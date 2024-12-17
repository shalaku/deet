<?php

namespace App\Http\Controllers\API\Order;

use App\Actions\MatchingOrderProcessAction;
use App\Actions\SendNotificationToUserAction;
use App\DTOs\CustomNotificationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CancelRequestMatchingOrderRequest;
use App\Http\Requests\MatchingRequest;
use App\Http\Requests\MatchingRequestDetail;
use App\Http\Requests\RequestMatchingDetailsRequest;
use App\Http\Requests\RequestMatchingOrderConfirmCastsRequest;
use App\Http\Resources\RequestMatchingResource;
use App\Jobs\OrderProcessJob;
use App\Models\Order\MatchingOrderDetail;
use App\Models\Order\MatchingOrder;
use App\Models\Cast\Cast;
use App\Models\Customer\Customer;
use App\Models\Order\RequestMatching;
use App\Models\Order\RequestMatchingDetail;
use app\Services\PushNotificationService;
use Illuminate\Http\Request;
use App\Traits\APIResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\MailHelper;
use App\Helpers\OrderHelper;

use Illuminate\Support\Facades\Log;

class RequestMatchingController extends Controller
{
    use APIResponse;

    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = (int) $request->get('page', 1); // ここでページ番号を整数に変換

        $user = Auth::user();


        if ($user->account_type === 'cast' || $user->account_type === 'customer') {
            $query = RequestMatching::query()->orderBy('requested_start_time', 'desc');
        } else {
            $query = RequestMatching::query()->orderBy('created_at', 'desc');
        }


        if ($user->account_type === 'cast') {
            $cast = Cast::where('user_id', $user->id)->first();
            if (empty($cast)) {
                return $this->errorResponse('Cast not found!', 404);
            }

            // $query->where('status', 501)

            $query->where('created_at', '>=', now()->subDay())
            ->where(function ($q) use ($cast) {
                $q->where('cast_rank', '[]') // cast_rank が空の配列のレコード
                  ->orWhereJsonContains('cast_rank', $cast->rank); // または cast_rank に $cast->rank が含まれているレコード
            });




        } elseif ($user->account_type === 'customer') {
            $customer = Customer::where('user_id', $user->id)->first();
            $query->where('customer_id', $customer->id);
        } elseif ($user->account_type === 'admin') {
            $limit = $request->get('limit', 100);
            $query->with(['customer']);

            // if (!empty($request->status)) {
            //     $query->where('status', '=', $request->status);
            // }

            if (!empty($request->cast_status)) {
                $query->whereHas('details.cast', function ($q) use ($request) {
                    $q->where('status', $request->cast_status);
                });
            }
            if (!empty($request->cast_work_status)) {
                $query->whereHas('details.cast', function ($q) use ($request) {
                    $q->where('work_status', $request->cast_work_status);
                });
            }
            if (!empty($request->cast_live_status)) {
                $query->whereHas('details.cast', function ($q) use ($request) {
                    $q->where('live_status', $request->cast_live_status);
                });
            }

            if (!empty($request->location)) {
                $query->where('area_name', '=', $request->location);
            }

        } else {
            if ($user->account_type != 'admin') {
                return $this->errorResponse('This user is not allowed to access this data!', 403);
            }
        }

        $query->when($request->filled('status'), function ($q) use ($request) {
            $statuses = explode(',', $request->status); // カンマ区切りの文字列を配列に変換
            $q->whereIn('status', $statuses);
        });

        $requestMatchings = $query->paginate($limit, ['*'], 'page', $page);

        $response = [
            'requests' => RequestMatchingResource::collection($requestMatchings),
            'total' => $requestMatchings->total(),
            'pages' => $requestMatchings->lastPage(),
        ];

        return $this->successResponse('Matching requests retrieved successfully.', $response);
    }

    public function getDesignatedRequest(Request $request)
    {
        $user = Auth::user();
        if ($user->account_type === 'cast') {
            $cast = Cast::where('user_id', $user->id)->first();
            if (empty($cast)) {
                return $this->errorResponse('Cast not found!', 404);
            }
            // Fetch matching records based on cast_id
            $matchingDetails = RequestMatchingDetail::with('request')
                ->where('cast_id', $cast->id)
                ->where('status', 513)
                ->get();

            if ($matchingDetails->isEmpty()) {
                return response()->json([]); // 空の配列を返す
            }

            return response()->json($matchingDetails);
        } else {
            return response()->json(['message' => 'Unauthorized'], 403); // 適切なエラーメッセージを返す
        }
    }

    public function requestMatchingOrder(MatchingRequest $request)
    {
        $validatedData = $request->validated();
        $selected_cast_ids = $validatedData['selected_cast_ids'] ?? [];
        unset($validatedData['selected_cast_ids']);

        $validatedData['shown_id'] = OrderHelper::generateShownId('request');

        DB::beginTransaction(); // トランザクション開始

        try {
            $requestMatching = RequestMatching::create($validatedData);
            DB::commit(); // トランザクションコミット

            $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $requestMatching->requested_start_time);
            list($hours, $minutes) = explode(':', $requestMatching->requested_matching_term);
            $endDateTime = (clone $startDateTime)->addHours($hours)->addMinutes($minutes);

            if (!empty($selected_cast_ids)) {
                $selectedCastIds = array_map('trim', explode(',', $selected_cast_ids));
                $rank_point_arr = [
                    'BLACK' => 10000,
                    'PLATINUM' => 8000,
                    'GOLD' => 6000,
                ];



                $isLateNight = (
                    ($startDateTime->hour >= 0 && ($startDateTime->hour < 5 || ($startDateTime->hour == 5 && $startDateTime->minute == 0))) ||
                    ($endDateTime->hour >= 0 && ($endDateTime->hour < 5 || ($endDateTime->hour == 5 && $endDateTime->minute == 0))) ||
                    ($startDateTime->hour < 5 && $endDateTime->hour > 5) ||
                    ($startDateTime->hour > 5 && $endDateTime->hour > 5 && $startDateTime->hour > $endDateTime->hour)
                );

                foreach ($selectedCastIds as $castId) {
                    $cast = Cast::find($castId);
                    if (!$cast || !array_key_exists($cast->rank, $rank_point_arr)) {
                        return $this->errorResponse('Request Failed!', 403, ['error' => "無効な女性会員またはクラスです。"]);
                    }

                    RequestMatchingDetail::create([
                        'matching_id' => $requestMatching->id,
                        'cast_id' => $castId,
                        'status' => 513,
                        'designated_point' => 1500,
                    ]);
                }
            }

            OrderProcessJob::dispatch($requestMatching->id)->onQueue('on-call-cast-expiration')
                ->delay(now()->addMinutes(15));

            DB::commit(); // トランザクションコミット
            // MailHelper::sendEmail('urata@texhnolyze.biz', 'コール案件申し込み発生', 'コールの申し込みがありました。：'.$requestMatching->id);
            $customer = Customer::find($validatedData['customer_id']);
            MailHelper::sendEmail(
                config('notify.mail_address'),
                'コール案件申し込み発生',
                'コールの申し込みがありました : ID'.$requestMatching->id."\n".
                '表示ID：'.$requestMatching->shown_id."\n".
                '開始予定時刻：'.$startDateTime."\n".
                '終了予定時刻：'.$endDateTime."\n".
                '時間：'.$requestMatching->requested_matching_term."\n".
                '希望女性会員人数：'.$validatedData['number_of_people']."\n".
                '男性会員：'.$customer->name_ja."(".$customer->nickname.")"."\n".
                '場所：'.$validatedData['area_name']."\n"
            );

            return $this->successResponse('マッチングリクエストが正常に作成されました。', $requestMatching, 201);
        } catch (\Exception $e) {
            DB::rollBack(); // エラーが発生した場合はロールバック
            return $this->errorResponse('Request Failed!', 500, ['error' => $e->getMessage()]);
        }
    }



    public function requestMatchingDetail(MatchingRequestDetail $request)
    {

        $validatedData = $request->validated();

        DB::beginTransaction();
        try {
        $checkRequestDetail = RequestMatchingDetail::where('matching_id', $request->matching_id)
            ->where('cast_id', $request->cast_id)
            ->first();
        if (!empty($checkRequestDetail) && !$request->designated) {
            return $this->errorResponse('Request Failed!', 403,  ['error' => 'already requested']);
        }

        if (!$request->designated) {
            // designatedが無効な場合、新しいレコードを作成
            $requestMatchingDetail = RequestMatchingDetail::create($validatedData);
        } else {
            // designatedが有効な場合、既存のレコードを更新
            $requestMatchingDetail = RequestMatchingDetail::where('matching_id', $validatedData['matching_id']) // 適切な条件を指定
                ->first(); // 既存のレコードを取得

            if ($requestMatchingDetail) {
                // レコードが存在する場合、statusを510に更新
                $requestMatchingDetail->update(['status' => 510]);
            } else {
                // レコードが存在しない場合の処理（必要に応じて）
                // 例: エラーメッセージを返す
                return $this->errorResponse('指定されたレコードが見つかりません。', 404);
            }
        }

        DB::commit();


        $checkRequest = RequestMatching::where('id', $request->matching_id)->first();
        $checkRequestedCount  = RequestMatchingDetail::where('matching_id', $request->matching_id)->where('status', 510)->count();
        if($checkRequestedCount == $checkRequest->number_of_people) {

        }

        $customer = Customer::find($checkRequest->customer_id);
        $cast = Cast::find($request->cast_id);
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $checkRequest->requested_start_time);
        list($hours, $minutes) = explode(':', $checkRequest->requested_matching_term);
        $endDateTime = (clone $startDateTime)->addHours($hours)->addMinutes($minutes);
        MailHelper::sendEmail(
            config('notify.mail_address'),
            'コール案件応募発生',
            'コール案件に応募がありました : ID'.$checkRequest->id."\n".
            '応募女性会員：'.$cast->name_ja."(".$cast->nickname.")"."\n".
            '開始予定時刻：'.$startDateTime."\n".
            '終了予定時刻：'.$endDateTime."\n".
            '時間：'.$checkRequest->requested_matching_term."\n".
            '希望女性会員人数：'.$checkRequest->number_of_people."\n".
            '現在の応募人数：'.$checkRequestedCount."\n".
            '男性会員：'.$customer->name_ja."(".$customer->nickname.")"."\n".
            '場所：'.$checkRequest->area_name."\n"
        );
        return $this->successResponse('Matching Request Created Successfully', $requestMatchingDetail, 201);
    } catch (\Throwable $exception) {
        DB::rollBack();
        return $this->errorResponse('Matching Request Created Failed', 500, ['error' => $exception->getMessage()]);

    }
    }

    public function updateStatus(RequestMatchingDetailsRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $requestMatchingData = RequestMatching::where('id', $data['request_matching_id'])->first();

            $customer = Customer::where('id', $requestMatchingData->customer_id)->first();

            if ($data['type'] == 'reject') {
                RequestMatchingDetail::where('matching_id', $data['request_matching_id'])->where('cast_id', $data['cast_id'])->update(['status' => 514]);

                $cast = Cast::where('id', $data['cast_id'])->first();
                if ($cast) {
                    (new SendNotificationToUserAction())->sendCustomerNotification($cast, ['title' => 'disbanded notification', 'body' => $cast->nickname . 'has disbanded']);
                    (new SendNotificationToUserAction())->sendCustomerNotification($customer, ['title' => 'disbanded notification', 'body' => $cast->nickname . 'has disbanded']);
                }

            } else {
                RequestMatchingDetail::create([
                    'matching_id' => $data['request_matching_id'],
                    'status' => 512,
                    'cast_id' => $data['cast_id'],
                ]);
            }

            $requestMatchingDetails = RequestMatchingDetail::where('matching_id', $data['request_matching_id'])->where('status', 512)->get();

            $requestCastCount  = $requestMatchingData->number_of_people;
            $castCount = count($requestMatchingDetails);

            if ($requestCastCount == $castCount) {
                $requestMatchingData->update(['status' => 502]);

                (new MatchingOrderProcessAction())->execute($requestMatchingData, $requestMatchingDetails);
                SendNotificationToUserAction::sendNotificationToCastFormRequestMatchingDetails($requestMatchingDetails);
                SendNotificationToUserAction::sendNotificationToCustomerFromRequestMatching($requestMatchingData);
            }

            DB::commit();
            return $this->successResponse('Matching Request Updated Successfully', []);

        } catch (\Throwable $exception) {
            DB::rollBack();
            return $this->errorResponse('Matching Request Update Failed', 500, ['error' => $exception->getMessage()]);

        }
    }

    public function cancelOrder(CancelRequestMatchingOrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            RequestMatching::where('id', $data['request_matching_id'])->update(['status' => 503]);
            RequestMatchingDetail::where('matching_id', $data['request_matching_id'])->update(['status' => 511]);

            DB::commit();
            return $this->successResponse('Matching Order Canceled Successfully', []);
        } catch (\Throwable $exception) {
            return $this->errorResponse('Matching Order Canceled Failed', 500, ['error' => $exception->getMessage()]);
        }
    }

    public function confirmOrder(CancelRequestMatchingOrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $requestMatchingData = RequestMatching::where('id', $data['request_matching_id'])->first();
            $requestMatchingDetails = RequestMatchingDetail::where('matching_id', $data['request_matching_id'])->get();

            $requestMatchingData->update([
                'status' => 502,
            ]);

            $requestMatchingDetails->each(function ($detail) {
                $detail->update(['status' => 512]);
            });

            (new MatchingOrderProcessAction())->execute($requestMatchingData, $requestMatchingDetails);

            DB::commit();
            return $this->successResponse('Matching Order Confirmed Successfully', []);

        } catch (\Throwable $exception) {
            DB::rollBack();
            return $this->errorResponse('Matching Order Confirmed Failed.', 500, ['error' => $exception->getMessage()]);
        }
    }

    public function castConfirm(RequestMatchingOrderConfirmCastsRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();

            // 512に更新
            RequestMatchingDetail::where('matching_id', $data['request_matching_id'])
                ->whereIn('cast_id', $data['cast_ids'])
                ->update(['status' => 512]);

            // 514に更新
            RequestMatchingDetail::where('matching_id', $data['request_matching_id'])
                ->whereNotIn('cast_id', $data['cast_ids'])
                ->update(['status' => 514]);

            // 存在しない場合は512で作成
            foreach ($data['cast_ids'] as $cast_id) {
                RequestMatchingDetail::updateOrCreate(
                    [
                        'matching_id' => $data['request_matching_id'],
                        'cast_id' => $cast_id,
                        'status' => 512,
                    ]
                );
            }
            DB::commit();

            $requestMatchingData = RequestMatching::where('id', $data['request_matching_id'])->first();
            $requestMatchingDetails = RequestMatchingDetail::where('matching_id', $data['request_matching_id'])
                ->where('status', 512)
                ->get();

            $requestCastCount = $requestMatchingData->number_of_people;
            $castCount = count($requestMatchingDetails);

            if ($requestCastCount == $castCount) {
                $requestMatchingData->update(['status' => 502]);
                (new MatchingOrderProcessAction())->execute($requestMatchingData, $requestMatchingDetails);
            }

            DB::commit();
            return $this->successResponse('Matching Request Updated Successfully', []);

        } catch (\Throwable $exception) {
            DB::rollBack();
            return $this->errorResponse('Matching Request Update Failed', 500, ['error' => $exception->getMessage()]);
        }
    }
}
