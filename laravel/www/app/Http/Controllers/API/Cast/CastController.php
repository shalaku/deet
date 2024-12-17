<?php

namespace App\Http\Controllers\API\Cast;

use App\Actions\CastLandingPage;
use App\Actions\CreateUserForChatAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CastRequest;
use App\Http\Requests\CastUpdateRequest;
use App\Http\Resources\CastResource;
use App\Models\PointHistory;
use App\Models\Cast\Cast;
use App\Models\Cast\CastBankAccDetails;
use App\Models\Cast\CastImage;
use App\Models\Image;
use App\Models\Status;
use App\Models\User;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class CastController extends Controller
{
    use APIResponse;


    public function aggregateOrdersMonthly(Request $request)
    {
        // 認証ユーザーを取得
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('ユーザーが認証されていません', 401);
        }
        if ($user->account_type != 'cast') {
            return $this->errorResponse('This user is not allowed to access this data!', 403);
        }
        $cast = Cast::where('user_id', $user->id)->first();
        if (empty($cast)) {
            return $this->errorResponse('Cast not found!', 404);
        }

        $cast_id = $cast->id;

        // 現在の月の開始日と終了日を取得
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // finished
        $orders_finished = DB::table('matching_order_details')
            ->join('matching_orders', 'matching_order_details.order_id', '=', 'matching_orders.id')
            ->where('matching_order_details.cast_id', $cast_id)
            ->where('matching_order_details.order_acception_status', 701)
            ->whereBetween('matching_order_details.start_date_time', [$startOfMonth, $endOfMonth])
            ->whereNotNull('matching_order_details.end_date_time')
            ->selectRaw('COALESCE(count(*), 0) as order_count, COALESCE(sum(total_point), 0) as total_sales')
            ->first();

        // not finished
        $orders_planned = DB::table('matching_order_details')
            ->join('matching_orders', 'matching_order_details.order_id', '=', 'matching_orders.id')
            ->where('matching_order_details.cast_id', $cast_id)
            // ->whereIn('matching_orders.status', [601, 602])
            ->where('matching_order_details.order_acception_status', 701)
            ->whereBetween('matching_orders.planned_meeting_date_time', [$startOfMonth, $endOfMonth])
            ->whereNull('matching_order_details.end_date_time')
            ->selectRaw('COALESCE(count(*), 0) as order_count, COALESCE(sum(total_point), 0) as total_sales')
            ->first();

        return $this->successResponse('aggregateOrdersMonthly retrieved successfully.', [
            'orders_finished' => [
                'order_count' => $orders_finished->order_count,
                'total_sales' => $orders_finished->total_sales,
            ],
            'orders_planned' => [
                'order_count' => $orders_planned->order_count,
                'total_sales' => $orders_planned->total_sales,
            ],
        ]);
    }


    public function index(Request $request)
    {
        $limit = $request->get('limit', 100);
        $page = $request->get('page', 1);

        $query = Cast::query();

        if (!empty($request->cast_id)) {
            $query->where('id', '=', $request->cast_id);
        }

        if (!empty($request->cast_id)) {
            $query->where('id', '=', $request->cast_id);
        }

        if (!empty($request->status)) {
            $query->where('status', '=', $request->status);
        } else {
            $query->where('status', '!=', 103);
        }
        if (!empty($request->work_status)) {
            $query->where('work_status', '=', $request->work_status);
        }
        if (!empty($request->live_status)) {
            $query->where('live_status', '=', $request->live_status);
        }
        if (!empty($request->name_ja)) {
            $query->where('name_ja', 'like', '%'.$request->name_ja.'%');
        }


        if (!empty($request->name_kana)) {
            $query->where('name_kana', 'like', '%'.$request->name_kana.'%');
        }
        if (!empty($request->nickname)) {
            $query->where('nickname', 'like', '%'.$request->nickname.'%');
        }

        if (!empty($request->birthday)) {
            $query->where('birthday', '=', $request->birthday);
        }

        if (!empty($request->basic_point_price)) {
            $query->where('basic_point_price', '=', $request->basic_point_price);
        }

        if (!empty($request->introducer_id)) {
            $query->where('introducer_id', '=', $request->introducer_id);
        }

        if (!empty($request->person_in_charge_id)) {
            $query->where('person_in_charge_id', '=', $request->person_in_charge_id);
        }

        if (!empty($request->footwork)) {
            $query->where('footwork', '=', $request->footwork);
        }

        if (!empty($request->alcohol)) {
            $query->where('alcohol', '=', $request->alcohol);
        }

        if (!empty($request->registered_date)) {
            $query->where('registered_date', '=', $request->registered_date);
        }

        if (!empty($request->prefecture)) {
            $query->where('prefecture', 'like', '%'.$request->prefecture.'%');
        }

        if (!empty($request->three_size_b)) {
            $query->where('three_size_b', '>=', $request->three_size_b);
        }
        if (!empty($request->three_size_w)) {
            $query->where('three_size_w', '>=', $request->three_size_w);
        }
        if (!empty($request->three_size_h)) {
            $query->where('three_size_h', '>=', $request->three_size_h);
        }
        if (!empty($request->cup_size)) {
            $query->where('cup_size', '=', $request->cup_size);
        }
        if (!empty($request->tag_of_spec)) {
            $tags = explode(',', $request->tag_of_spec);
            $query->where(function ($query) use ($tags) {
                foreach ($tags as $tag) {
                    $encodedTag = json_encode([$tag]);
                    $query->whereRaw('JSON_CONTAINS(tag_of_spec, ?)', [$encodedTag]);
                }
            });
        }
        // print_r($query->toSql());
        if (!empty($request->height)) {
            $minHeight = explode('-', $request->height)[0] ?? 145;
            $maxHeight = explode('-', $request->height)[1] ?? 180;

            $query->where('height', '>=', $minHeight)->where('height', '<=', $maxHeight);
        }

        if (!empty($request->rank)) {
            $ranks = explode(',', $request->rank); // カンマ区切りの文字列を配列に変換
            $query->whereIn('rank', $ranks);
        }

        if (!empty($request->age)) {
            $minAge = explode('-', $request->age)[0] ?? 0;
            $maxAge = explode('-', $request->age)[1] ?? 99;

            if (!is_null($minAge)) {
                $query->whereRaw('FLOOR(DATEDIFF(CURDATE(), birthday) / 365) >= ?', [$minAge]);
            }

            if (!is_null($maxAge)) {
                $query->whereRaw('FLOOR(DATEDIFF(CURDATE(), birthday) / 365) <= ?', [$maxAge]); // 修正: 364を365に変更
            }
        }

        // selected_cast_ids が存在する場合の処理
        if (!empty($request->selected_cast_ids)) {
            $selectedIds = explode(',', $request->selected_cast_ids); // カンマ区切りのIDを配列に変換
            $query->whereIn('id', $selectedIds); // IDが配列に含まれる場合にフィルタリング
        }

        $casts = $query->paginate($limit, ['*'], 'page', $page);

        $response = [
            'casts' => CastResource::collection($casts),
            'total' => $casts->total(),
            'pages' => $casts->lastPage(),
        ];

        return $this->successResponse('Casts retrieved successfully.', $response);
    }


    public function store(CastRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();
            $user = User::where('email', '=', $data['email'])->first();
            if (!empty($user)) {
                return $this->errorResponse('User Already Exists.');
            }
            $user = $this->createUser($request->all());

            // Convert the tag_of_spec array to a JSON string
            if (isset($data['tag_of_spec']) && is_array($data['tag_of_spec'])) {
                $data['tag_of_spec'] = json_encode($data['tag_of_spec']);
            }

            $cast = $this->createCast($data, $user);
            DB::commit();

            return $this->successResponse('Cast created successfully.', $cast);
        } catch (\Throwable $th) {
            DB::rollback();

            return $this->errorResponse('Cast Create Failed', 400, ['error' => $th->getMessage()]);
        }
    }

    public function show($castId)
    {
        $response = [];
        if ($castId == 'profile') {
            $user = Auth::user();

            if ($user->account_type != 'cast') {
                return $this->errorResponse('This user is not allowed to access this data!', 403);
            }

            $cast = Cast::where('user_id', $user->id)->first();

            if (empty($cast)) {
                return $this->errorResponse('Cast not found!', 404);
            }

            $castId = $cast->id;

            $response = new CastResource($cast);
        } else {
            $cast = Cast::where('id', $castId)->first();
            if (empty($cast)) {
                return $this->errorResponse('Cast not found!', 404);
            }
            $response = new CastResource($cast);
        }

        return $this->successResponse('Cast retrieved successfully.', $response);
    }

    public function update(CastUpdateRequest $request, $id)
    {
        if ($id != 'update') {
            return $this->updateCast($request->all(), $id);
        }

        $user = Auth::user();

        if ($user->account_type != 'cast') {
            return $this->errorResponse('This user is not allowed to access this data!', 403);
        }

        $cast = Cast::where('user_id', $user->id)->first();

        return $this->updateCast($request->all(), $cast->id);
    }

    private function updateCast(array $data, $id = null)
    {
        DB::beginTransaction();

        try {
            $cast = Cast::where('id', $id)->first();
            $user = User::where('id', $cast['user_id'])->first();

            if (empty($cast)) {
                return $this->errorResponse('Cast not found', 404);
            }

            if (empty($user)) {
                return $this->errorResponse('User not found', 404);
            }

            $userData = $this->getUserDataFormRequest($data);
//            $castData = $this->getCastData($data);

            $user->update($userData);
            $cast->update($data);

            if (!empty($data['bank_details'])) {
                $castBankDetails = CastBankAccDetails::where('cast_id', $id)->first();
                if (empty($castBankDetails)) {
                    $data['bank_details']['cast_id'] = $id;
                    CastBankAccDetails::create($data['bank_details']);
                } else {
                    $castBankDetails->update($data['bank_details']);
                }
            }

            DB::commit();

            return $this->successResponse('Cast Update successfully.', $cast);
        } catch (\Throwable $th) {
            DB::rollback();

            return $this->errorResponse('Cast Update Failed', 400, ['error' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $cast = Cast::where('id', $id)->first();

        if (empty($cast)) {
            return $this->errorResponse('Cast Not Found.', 404);
        }
        $user = User::where('id', $cast->user_id)->first();

        if (!empty($user)) {
            // Generate a unique postfix for the email
            $originalEmail = $user->email;
            $emailParts = explode('@', $originalEmail);
            $newEmail = $emailParts[0].'-del-'.uniqid().'@'.$emailParts[1];

            // Update the email address
            $user->update(['email' => $newEmail]);
        }
        $cast->status = 103;
        $cast->save();

        return $this->successResponse('Cast Delete Successfully.');
    }


    public function createUser(array $data)
    {
        return User::create([
            'name_ja' => $data['name_ja'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 1,
            'account_type' => 'cast',
            'user_type' => '-',
            'uuid' => (string)Str::uuid(),
        ]);
    }

    public function createCast(array $data, $user)
    {
        $data['user_id'] = $user->id;

        $cast = Cast::create($data);

        $chatUser = (new CreateUserForChatAction())->createChatUser('cast_'.$cast->id, $data['nickname']);
        User::where('id', $user->id)->update([
            'user_sign' => $chatUser->userSig,
            'user_id' => $chatUser->userId,
        ]);

        return $cast;
    }

    public function getUserDataFormRequest(array $data): array
    {
        $userData = [];
        if (isset($data['name_ja'])) {
            $userData['name_ja'] = $data['name_ja'];
        }

        return $userData;
    }

    public function homepage(Request $request)
    {
        $user = Auth::user();

        if ($user->account_type != 'cast') {
            return $this->errorResponse('This user is not allowed to access this data!', 403);
        }

        $cast = Cast::where('user_id', $user->id)->first();

        if (empty($cast)) {
            return $this->errorResponse('Cast not found!', 404);
        }

        $response = (new CastLandingPage())->execute($cast);

        return $this->successResponse('Cast Landing Page Data', $response);
    }
}
