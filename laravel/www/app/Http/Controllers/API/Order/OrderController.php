<?php

namespace App\Http\Controllers\API\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\DirectCallCastRequest;
use App\Models\PointHistory;
use App\Models\Cast\Cast;
use App\Models\Customer\Customer;
use App\Models\Order\MatchingOrderDetail;
use Illuminate\Http\Request;
use App\Http\Resources\MatchingOrderResource;
use App\Models\Order\MatchingOrder;
use App\Traits\APIResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\MailHelper;
use App\Helpers\OrderHelper;
use App\Jobs\OrderExtendedJob;

class OrderController extends Controller
{
    use APIResponse;

    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
    
        $user = Auth::user();
        if ($user->account_type === 'cast' || $user->account_type === 'customer') {
            $query = MatchingOrder::with('details')->orderBy('planned_meeting_date_time', 'asc');
        } else {
            $query = MatchingOrder::with('details')->orderBy('id', 'desc');
        }
        // $query = MatchingOrder::with('details')->orderBy('id', 'desc');
        // $query->orderBy('planned_meeting_date_time', 'asc'); // 'asc' または 'desc' を指定
    
    
        if ($user->account_type === 'cast') {
            $cast = Cast::where('user_id', $user->id)->first();
            if (empty($cast)) {
                return $this->errorResponse('Cast not found!', 404);
            }
    
            if ($request->search === 'confirmed') {
                $query->where(function ($q) use ($cast) {
                    $q->whereIn('status', [601, 602])
                      ->whereHas('details', function ($q) use ($cast) {
                          $q->where('cast_id', $cast->id)
                            ->whereNull('start_date_time');
                      });
                })->with(['details' => function ($q) use ($cast) {
                    $q->where('cast_id', $cast->id)
                      ->whereNull('start_date_time');
                }]);
            } else if ($request->search === 'progress') {
                $query->where(function ($q) use ($cast) {
                    $q->whereIn('status', [602])
                      ->whereHas('details', function ($q) use ($cast) {
                          $q->where('cast_id', $cast->id)
                            ->whereNotNull('start_date_time')
                            ->whereNull('end_date_time');
                      });
                })->with(['details' => function ($q) use ($cast) {
                    $q->where('cast_id', $cast->id)
                        ->whereNotNull('start_date_time')
                      ->whereNull('end_date_time');
                }]);
            } else {
                $query->whereHas('details', function ($q) use ($cast) {
                    $q->where('cast_id', $cast->id);
                })->with(['details' => function ($q) use ($cast) {
                    $q->where('cast_id', $cast->id);
                }]);
            }
                        

        } elseif ($user->account_type === 'customer') {
            if (!empty($request->multi_status)) {
                $statuses = explode(',', $request->multi_status);
                $query->where(function($q) use ($statuses) {
                    foreach ($statuses as $status) {
                        $q->orWhere('status', $status);
                    }
                });
            }            
            $customer = Customer::where('user_id', $user->id)->first();
            $query->where('customer_id', $customer->id);
            $query->with(['details.cast']);
        } elseif ($user->account_type === 'admin') {
            $limit = $request->get('limit', 100);
            $query->with(['customer']);
            
            if (!empty($request->status)) {
                $query->where('status', '=', $request->status);
            }

            if (!empty($request->multi_status)) {
                $statuses = explode(',', $request->multi_status);
                $query->where(function($q) use ($statuses) {
                    foreach ($statuses as $status) {
                        $q->orWhere('status', $status);
                    }
                });
            }  

            if (!empty($request->min_month)) {
                $startOfMonth = $request->min_month . '-01 00:00:00';
                $query->where('start_date_time', '>=', $startOfMonth);
            }
            
            if (!empty($request->max_month)) {
                $endOfMonth = date("Y-m-t", strtotime($request->max_month . '-01')) . ' 23:59:59';
                $query->where('start_date_time', '<=', $endOfMonth);
            }       

            if (!empty($request->min_week)) {
                $startOfWeek = date("Y-m-d 00:00:00", strtotime($request->min_week . ' monday'));
                $query->where('start_date_time', '>=', $startOfWeek);
            }
            
            if (!empty($request->max_week)) {
                $endOfWeek = date("Y-m-d 23:59:59", strtotime($request->max_week . ' sunday'));
                $query->where('start_date_time', '<=', $endOfWeek);
            }
            
            if (!empty($request->min_date)) {
                $startDate = $request->min_date . ' 00:00:00';
                $query->where('start_date_time', '>=', $startDate);
            }
            
            if (!empty($request->max_date)) {
                $endDate = $request->max_date . ' 23:59:59';
                $query->where('start_date_time', '<=', $endDate);
            }

            if (!empty($request->order_type)) {
                $query->where('order_type', '=', $request->order_type);
            }  
                        
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
                $query->where('place_desc', '=', $request->location);
            }

            // order_type 別の total_point の集計結果を取得
            $orderTypePoints = $query->get()->groupBy('order_type')->map(function ($orders) {
                return $orders->sum(function ($order) {
                    return $order->details->sum('total_point');
                });
            });

            $response['order_type_points'] = $orderTypePoints;

            // details の extra_charge の集計結果を取得
            $extraChargeSum = $query->get()->sum(function ($order) {
                return $order->details->sum('extra_charge');
            });

            $response['extra_charge_sum'] = $extraChargeSum;

        } else {
            if ($user->account_type != 'admin') {
                return $this->errorResponse('This user is not allowed to access this data!', 403);
            }
        }
    
        $query->when($request->filled('order_type'), function ($q) use ($request) {
            $q->where('order_type', $request->order_type);
        });
    
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });
    
        $query->when($request->filled('history'), function ($q) use ($request) {
            $q->where(function ($query) {
                $query->where('status', 603)
                      ->orWhere('status', 604);
            });
        });
    
        $matchingOrders = $query->paginate($limit, ['*'], 'page', $page);
    
        $response['orders'] = MatchingOrderResource::collection($matchingOrders);
        $response['total'] = $matchingOrders->total();
        $response['pages'] = $matchingOrders->lastPage();
        // $response = [
        //     'orders' => MatchingOrderResource::collection($matchingOrders),
        //     'total' => $matchingOrders->total(),
        //     'pages' => $matchingOrders->lastPage(),
        // ];
    
        return $this->successResponse('Matching orders retrieved successfully.', $response);
    }


    public function directCallCast(DirectCallCastRequest $request)
    {
        $data = $request->all();
        $cast_id = $data['cast_id'];

        unset($data['cast_id']);

        $matchingOrderData = $data;
        $matchingOrderData['shown_id'] = OrderHelper::generateShownId('order');

        DB::beginTransaction();

        try {
            $matchingOrderData['status'] = $matchingOrderData['status'] ?? 600;
            $orderData = MatchingOrder::create($matchingOrderData);
            if (!empty($cast_id)) {

                $cast = Cast::find($cast_id);

                $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $matchingOrderData['planned_meeting_date_time']);
                list($hours, $minutes) = explode(':', $matchingOrderData['planned_meeting_time']);
                $endDateTime = (clone $startDateTime)->addHours($hours)->addMinutes($minutes);

                $calculated = OrderHelper::calculateOrderPoints(
                    $matchingOrderData['planned_meeting_time'],
                    $startDateTime,
                    $endDateTime,
                    $cast->basic_point_price,
                    $designated=false, 
                );
                // return $this->errorResponse('Order Creation Failed!', 500,  ['error' => $calculated]);
                $details['order_acception_status'] = 703;
                $details['order_id'] = $orderData->id;
                $details['cast_id'] = $cast_id;
                $details['applied_point'] = $cast->basic_point_price;
                $details['designated_point'] = $calculated['designated_point'];
                $details['mid_night_fee'] = $calculated['mid_night_fee'];
                $details['extra_charge'] = $calculated['extra_charge'];
                $details['total_point'] = $calculated['total_point'];
                MatchingOrderDetail::create($details);

            }

            DB::commit();

            $customer = Customer::find($matchingOrderData['customer_id']);
            MailHelper::sendEmail(
                config('notify.mail_address'), 
                'Deet案件申し込み発生', 
                'Deetの申し込みがありました : ID'.$orderData->id."\n".
                '表示ID：'.$orderData->shown_id."\n".
                '開始予定時刻：'.$startDateTime."\n".
                '終了予定時刻：'.$endDateTime."\n".
                '時間：'.$matchingOrderData['planned_meeting_time']."\n".
                '女性会員：'.$cast->name_ja."(".$cast->nickname.")"."\n".
                '男性会員：'.$customer->name_ja."(".$customer->nickname.")"."\n".
                '場所：'.$matchingOrderData['place_street']."\n"
            );

            return $this->successResponse('Order Created Successfully', [], 201);
        } catch (\Throwable $exception) {
            DB::rollBack();

            return $this->errorResponse('Order Creation Failed!', 500,  ['error' => $exception->getMessage()]);
        }
        


    }

    public function leaveOrder(Request $request)
    {
        DB::beginTransaction();
    
        try {
            // マッチングオーダー詳細を取得
            $endDateTime = now(); // 現在の時間を終了時間とする
            $matchingOrderDetail = MatchingOrderDetail::where('order_id', $request->order_id)
                ->where('cast_id', $request->cast_id)
                ->where('order_acception_status', 701)
                ->firstOrFail();
    
            // 終了日時を更新
            $matchingOrderDetail->update([
                'end_date_time' => $endDateTime
            ]);

            // 女性会員とオーダーを取得
            $cast = Cast::findOrFail($request->cast_id);
            $matchingOrder = MatchingOrder::where('id', $request->order_id)->first();
            $customer = Customer::findOrFail($matchingOrder->customer_id);
    
            // 開始時間と終了時間を取得
            $startDateTime = Carbon::parse($matchingOrderDetail->start_date_time);
            $endDateTime = Carbon::parse($endDateTime); // すでにCarbonオブジェクトですが、明示的に変換
    
            $calculated = OrderHelper::calculateOrderPoints(
                $matchingOrder->planned_meeting_time,
                $startDateTime,
                $endDateTime,
                $matchingOrderDetail->applied_point,
                $matchingOrderDetail->designated_point
            );
    
            // total_pointを更新
            $matchingOrderDetail->extra_charge = $calculated['extra_charge'];
            $matchingOrderDetail->mid_night_fee = $calculated['mid_night_fee'];
            $matchingOrderDetail->designated_point = $calculated['designated_point'];
            $matchingOrderDetail->total_point = $calculated['total_point'];
            $matchingOrderDetail->save();
    
            // ポイント履歴を作成
            PointHistory::create([
                'user_id' => $cast->user_id,
                'order_id' => $request->order_id,
                'point_amount' => $calculated['total_point'],
            ]);
            PointHistory::create([
                'user_id' => $customer->user_id,
                'order_id' => $request->order_id,
                'point_amount' => $calculated['total_point'] * -1,
            ]);
    
            //ポイントを加算・減算
            $cast->points_holded += $calculated['total_point']; // 女性会員のポイントを加算
            $customer->points_holded -= $calculated['total_point']; // カスタマーのポイントを減算
            $cast->save();
            $customer->save();
    
            // マッチングオーダーの状態を更新
            $tmpOrderDetail = MatchingOrderDetail::where('order_id', $request->order_id)
                ->whereNull('end_date_time')
                ->first();
    
            if (empty($tmpOrderDetail)) {
                $matchingOrder->update([
                    'status' => 604,
                    'end_date_time' => $endDateTime
                ]);
            }
    
            // Update Cast work_status to 110
            $cast = Cast::where('id', $request->cast_id)->first();
            $cast->update([
                'work_status' => 110
            ]);   

            DB::commit();


            // $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $matchingOrder->start_date_time);
            // $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $matchingOrder->end_date_time);
            MailHelper::sendEmail(
                config('notify.mail_address'),
                '案件終了', 
                '女性会員が案件を離れました : ID'.$matchingOrder->id."\n".
                '案件タイプ：'.$matchingOrder->order_type."\n".
                '開始時刻：'.$matchingOrder->start_date_time."\n".
                '終了時刻：'.$endDateTime."\n".
                '女性会員：'.$cast->name_ja."(".$cast->nickname.")"."\n".
                '男性会員：'.$customer->name_ja."(".$customer->nickname.")"."\n".
                '場所：'.$matchingOrder->place_street."\n"
            );        
                


            return $this->successResponse('Matching Order updated successfully.', $matchingOrder);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse('Failed to update Matching Order.', 500, ['error' => $th->getMessage()]);
        }
    }
    

    public function startOrder(Request $request)
    {
        DB::beginTransaction();
    
        // Check if MatchingOrderDetail already has a start_date_time
        $matchingOrderDetail = MatchingOrderDetail::where('order_id', $request->order_id)
                                                  ->whereNotNull('start_date_time')
                                                  ->first();

        $matchingOrder = MatchingOrder::where('id', $request->order_id)->first();
        if (empty($matchingOrderDetail)) {
            // Update MatchingOrder if start_date_time is not already set
            // $matchingOrder = MatchingOrder::where('id', $request->order_id)->first();
            $matchingOrder->update([
                'status' => 602,
                'start_date_time' => now()
            ]);
        }
    
        // Update MatchingOrderDetail for the specific cast_id
        $matchingOrderDetail = MatchingOrderDetail::where('order_id', $request->order_id)
                                                  ->where('cast_id', $request->cast_id)
                                                  ->first();
    
        // Only update start_date_time if it is not already set
        if (is_null($matchingOrderDetail->start_date_time)) {
            $matchingOrderDetail->update([
                'start_date_time' => now()
            ]);
        }

        // Update Cast work_status to 111
        $cast = Cast::where('id', $request->cast_id)->first();
        $cast->update([
            'work_status' => 111
        ]);        

        DB::commit();   
        
        // Parse planned_meeting_time and add to current time
        list($hours, $minutes, $seconds) = explode(':', $matchingOrder->planned_meeting_time);
        $delayTime = now()->addHours($hours)->addMinutes($minutes)->addSeconds($seconds);

        OrderExtendedJob::dispatch($request->order_id)->onQueue('order-extension-monitoring')->delay($delayTime);
        
        return $this->successResponse('MatchingOrder updated successfully.', $matchingOrderDetail);
    } 

    public function acceptDeet(Request $request)
    {
        $matchingOrder = MatchingOrder::where('id', $request->order_id)->first();
        $matchingOrder->update(array('status'=>601));

        $matchingOrderDetail = MatchingOrderDetail::where('order_id', $request->order_id)->first();
        $matchingOrderDetail->update(array('order_acception_status'=>701));

        DB::commit();

        $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $matchingOrder->planned_meeting_date_time);
        list($hours, $minutes) = explode(':', $matchingOrder->planned_meeting_time);
        $endDateTime = (clone $startDateTime)->addHours($hours)->addMinutes($minutes);
        $customer = Customer::find($matchingOrder->customer_id);
        $cast = Cast::find($matchingOrderDetail->cast_id);        
        MailHelper::sendEmail(
            config('notify.mail_address'),
            'Deet案件承認', 
            'Deetが承認されました : ID'.$matchingOrder->id."\n".
            '開始予定時刻：'.$startDateTime."\n".
            '終了予定時刻：'.$endDateTime."\n".
            '時間：'.$matchingOrder->planned_meeting_time."\n".
            '女性会員：'.$cast->name_ja."(".$cast->nickname.")"."\n".
            '男性会員：'.$customer->name_ja."(".$customer->nickname.")"."\n".
            '場所：'.$matchingOrder->place_street."\n"
        );

        return $this->successResponse('matchingOrder Update successfully.', $matchingOrder);

    }

    public function rejectDeet(Request $request)
    {
        $matchingOrder = MatchingOrder::where('id', $request->order_id)->first();
        $matchingOrder->update(array('status'=>605));

        $matchingOrderDetail = MatchingOrderDetail::where('order_id', $request->order_id)->first();
        $matchingOrder->update(array('order_acception_status'=>702));

        DB::commit();

        return $this->successResponse('matchingOrder Update successfully.', $matchingOrder);
    }    

    public function saveAdminMemo(Request $request)
    {
        $matchingOrder = MatchingOrder::where('id', $request->order_id)->first();
        $matchingOrder->update(array('admin_memo'=>$request->admin_memo));

        DB::commit();

        return $this->successResponse('matchingOrder Update successfully.', $matchingOrder);
    }    

    public function saveCastMemo(Request $request)
    {
        $matchingOrderDetail = MatchingOrderDetail::where('id', $request->detail_id)->first();
        $matchingOrderDetail->update(array('cast_memo'=>$request->cast_memo));

        DB::commit();

        return $this->successResponse('matchingOrder Update successfully.', $matchingOrderDetail);
    }        
}
