<?php

namespace App\Http\Controllers\API;

// use App\Http\Requests\PointHistoryRequest;
use App\Http\Controllers\Controller;
use App\Models\PointHistory;
use App\Models\User;
use App\Models\Cast\Cast;
use App\Models\Customer\Customer;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointHistoryController extends Controller
{
    use APIResponse;


    public function index(Request $request)
    {
        try {
            $limit = $request->get('limit', 100);
            $page = $request->get('page', 1);
    
            $userId = $request->get('user_id', "");
            $userType = $request->get('user_type', "");
            $nameJa = $request->get('name_ja', "");
            $nickname = $request->get('nickname', "");
            $minPoint = $request->get('min_point', null);
            $maxPoint = $request->get('max_point', null);
            $min_month = $request->get('min_month', null);
            $max_month = $request->get('max_month', null);
            $min_week = $request->get('min_week', null);
            $max_week = $request->get('max_week', null);
            $min_date = $request->get('min_date', null);
            $max_date = $request->get('max_date', null);

            
            $user = Auth::user();
            if ($user->account_type === 'cast' || $user->account_type === 'customer') {
                if((int)$user->id !== (int)$userId){
                    return $this->errorResponse('User ID is notmatch.', 400);
                }
            }


            $query = PointHistory::with(['customer', 'cast','matching_order'])->orderBy('id', 'desc'); // ここでIDの降順に設定
    
            // user_idが指定されている場合
            if (!empty($userId)) {
                $query->where('user_id', $userId);
            }
    
            // user_typeに基づくフィルタリング
            if ($userType === 'cast') {
                $query->whereHas('cast', function ($q) use ($nameJa, $nickname) {
                    if ($nameJa) {
                        $q->where('name_ja', 'LIKE', "%{$nameJa}%");
                    }
                    if ($nickname) {
                        $q->where('nickname', 'LIKE', "%{$nickname}%");
                    }
                });
                // customerのフィルタリングを無効にする
                $query->whereDoesntHave('customer');
            } elseif ($userType === 'customer') {
                $query->whereHas('customer', function ($q) use ($nameJa, $nickname) {
                    if ($nameJa) {
                        $q->where('name_ja', 'LIKE', "%{$nameJa}%");
                    }
                    if ($nickname) {
                        $q->where('nickname', 'LIKE', "%{$nickname}%");
                    }
                });
            }
    
            // ポイント範囲のフィルタリング
            if (!is_null($minPoint)) {
                $query->where('point_amount', '>=', $minPoint);
            }
            if (!is_null($maxPoint)) {
                $query->where('point_amount', '<=', $maxPoint);
            }
    
            if (!is_null($min_month)) {
                $startOfMonth = $min_month . '-01 00:00:00';
                $query->where('created_at', '>=', $startOfMonth);
            }
    
            if (!is_null($max_month)) {
                $endOfMonth = date("Y-m-t", strtotime($max_month . '-01')) . ' 23:59:59';
                $query->where('created_at', '<=', $endOfMonth);
            }

            if (!is_null($min_week)) {
                $startOfWeek = date("Y-m-d 00:00:00", strtotime($max_week . ' monday'));
                $query->where('created_at', '>=', $startOfWeek);
            }
            
            if (!is_null($max_week)) {
                $endOfWeek = date("Y-m-d 23:59:59", strtotime($max_week . ' sunday'));
                $query->where('created_at', '<=', $endOfWeek);
            }            

            if (!is_null($min_date)) {
                $startDate = $min_date . ' 00:00:00';
                $query->where('created_at', '>=', $startDate);
            }
    
            if (!is_null($max_date)) {
                $endDate = $max_date . ' 23:59:59';
                $query->where('created_at', '<=', $endDate);
            }              

            // データを取得
            $data = $query->paginate($limit, ['*'], 'page', $page);
    
            // ポイントの合計を取得
            $totalPoints = $query->sum('point_amount');
            
            $totalPayAmount = $query->sum('pay_amount');
    
            $response = [
                'points' => $data,
                'total' => $data->total(),
                'pages' => $data->lastPage(),
                'total_points' => $totalPoints, // 合計ポイントを追加
                'total_pay_amount' => $totalPayAmount, // 合計ポイントを追加
            ];
            return $this->successResponse('PointHistory retrieved successfully.', $response);
    
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to retrieve PointHistory.', 500, ['error' => $th->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            // user_idが存在するか確認
            if (!$request->has('user_id')) {
                return $this->errorResponse('User ID is required.', 400);
            }
    
            // PointHistoryの作成
            $pointHistory = PointHistory::create($request->all());
    
            return $this->successResponse('PointHistory Created Successfully.', $pointHistory, 201);
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to create PointHistory.', 500, ['error' => $th->getMessage()]);
        }
    }

    public function storeWithHoldedPoint(Request $request)
    {
        try {
            // user_idが存在するか確認
            if (!$request->has('user_id')) {
                return $this->errorResponse('User ID is required.', 400);
            }
    
            // user_idからユーザー情報を取得
            $user = User::find($request->get('user_id'));
    
            if (empty($user)) {
                return $this->errorResponse('User Not Found.', 404);
            }
    
            // userのaccount_typeを確認して、対応するテーブルにポイントを加算
            $pointAmount = $request->get('point_amount');
    
            if ($user->account_type === 'cast') {
                // Castのpoints_holdedに加算
                $cast = Cast::where('user_id', $user->id)->first();
                if ($cast) {
                    $cast->points_holded += $pointAmount;
                    $cast->save();  // 更新を保存
                } else {
                    return $this->errorResponse('Cast Not Found for the given User.', 404);
                }
            } elseif ($user->account_type === 'customer') {
                // Customerのpoints_holdedに加算
                $customer = Customer::where('user_id', $user->id)->first();
                if ($customer) {
                    $customer->points_holded += $pointAmount;
                    $customer->save();  // 更新を保存
                } else {
                    return $this->errorResponse('Customer Not Found for the given User.', 404);
                }
            } else {
                return $this->errorResponse('Invalid account type.', 400);
            }
    
            // PointHistoryの作成
            $pointHistory = PointHistory::create($request->all());
    
            return $this->successResponse('PointHistory Created Successfully with Holded Points.', $pointHistory, 201);
    
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to create PointHistory with Holded Points.', 500, ['error' => $th->getMessage()]);
        }
    }
    

    public function show($id)
    {
        $pointHistory = PointHistory::where('id', $id)->first();

        if (empty($pointHistory)) {
            return $this->errorResponse('PointHistory Not Found.', 404);
        }

        return $this->successResponse('PointHistory Details.', $pointHistory);
    }

    public function update(Request $request, $id)
    {
        $pointHistory = PointHistory::where('id', $id)->first();

        if (empty($pointHistory)) {
            return $this->errorResponse('PointHistory Not Found.', 404);
        }

        if ($request->id != $id && PointHistory::where('id', $request->id)->first())
        {
            return $this->errorResponse('Invalid Id already exists.', 400);
        }

        $pointHistory->update($request->all());

        return $this->successResponse('PointHistory Update Successfully.', $pointHistory);
    }

    public function destroy($id)
    {
        $pointHistory = PointHistory::where('id', $id)->first();

        if (empty($pointHistory)) {
            return $this->errorResponse('PointHistory Not Found.', 404);
        }
        $pointHistory->delete();

        return $this->successResponse('PointHistory Delete Successfully.');
    }
}
