<?php

namespace App\Http\Controllers\API\Customer;

use App\Actions\CastAction;
use App\Actions\CreateUserForChatAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddFavCastRequest;
use App\Http\Requests\AutoPayRequest;
use App\Http\Requests\CustomerRegisterRequest;
use App\Http\Requests\CustomerUpdateRegisterRequest;
use App\Http\Resources\CastResource;
use App\Http\Resources\CustomerResource;
use App\Models\Cast\Cast;
use App\Models\Customer\Customer;
use App\Models\User;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Stripe\PaymentIntent;


class CustomerController extends Controller
{
    use APIResponse;


    public function index(Request $request)
    {
        $limit = $request->get('limit', 100);
        $page = $request->get('page', 1);

        $query =  Customer::query();
        if (!empty($request->name_ja)) {
            $query->where('name_ja', 'like', '%'.$request->name_ja.'%');
        }

        if (!empty($request->name_kana)) {
            $query->where('name_kana', 'like', '%'.$request->name_kana.'%');
        }

        if (!empty($request->nickname)) {
            $query->where('nickname', 'like', '%'.$request->nickname.'%');
        }

        if (!empty($request->nickname)) {
            $query->where('nickname', 'like', '%'.$request->nickname.'%');
        }

        if (!empty($request->birthday)) {
            $query->where('birthday', '=', $request->birthday);
        }

        if (!empty($request->introducer_id)) {
            $query->where('introducer_id', '=', $request->introducer_id);
        }

        if (!empty($request->person_in_charge_id)) {
            $query->where('person_in_charge_id', '=', $request->person_in_charge_id);
        }

        if (!empty($request->category_id)) {
            $query->where('category_id', '=', $request->category_id);
        }

        if (!empty($request->registered_date)) {
            $query->where('registered_date', '=', $request->registered_date);
        }

        if (!empty($request->tag_of_preference)) {
            $query->where('tag_of_preference', '=', $request->tag_of_preference);
        }

        $customers = $query->paginate($limit, ['*'], 'page', $page);

        $response = [
            'customers' => CustomerResource::collection($customers),
            'total' => $customers->total(),
            'pages' => $customers->lastPage(),
        ];
        return $this->successResponse('Customer List.', $response);
    }

    public function store(CustomerRegisterRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();
            $user = User::where('email', '=', $data['email'])->first();

            if (!empty($user)) {
                return $this->errorResponse('User Already Exists.');
            }
            $user = $this->createUser($request->all());

            $data = $request->all();
            $data['user_id'] = $user->id;
            $customer = Customer::create($data);
            $chatUser = (new CreateUserForChatAction())->createChatUser('customer_' . $customer->id, $data['nickname']);
            $user->update([
                'user_sign' => $chatUser->userSig,
                'user_id' => $chatUser->userId
            ]);

            DB::commit();
            return $this->successResponse('Customer Create Successfully.', $customer, 201);

        } catch (\Throwable $e) {

            DB::rollback();
            return $this->errorResponse('Customer Registration Failed.', 500, ['error' => $e->getMessage()]);
        }

    }

    private function createUser(array $data)
    {
        return User::create([
            'name_ja' => $data['name_ja'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 1,
            'account_type' => 'customer',
            'user_type' => '-',
            'uuid' => (string) Str::uuid(),
        ]);
    }
    public function show($customerId)
    {
        $response = [];
        if ($customerId == 'profile') {
            $user = Auth::user();

            if ($user->account_type != 'customer') {
                return $this->errorResponse('This user is not allowed to access this data!', 403);
            }

            $customer = Customer::where('user_id', $user->id)->first();

            if (empty($customer)) {
                return $this->errorResponse('Customer not found!', 404);
            }

            $response = new CustomerResource($customer);

        } else {
            $customer = Customer::where('id', $customerId)->first();
            if (empty($customer)) {
                return $this->errorResponse('Customer not found!', 404);
            }
            $response = new CustomerResource($customer);
        }

        return $this->successResponse('Customer retrieved successfully.', $response);
    }

    public function update(CustomerUpdateRegisterRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $customer = Customer::where('id', $id)->first();


            if (empty($customer)) {
                return $this->errorResponse('Customer Not Found.', 404);
            }

            $user = User::where('id', $customer['user_id'])->first();

            if (empty($user)) {
                return $this->errorResponse('User not found', 404);
            }

            $data = $request->all();
            $userData = $this->getUserDataFormRequest($data);

            $user->update($userData);
            $customer->update($data);

            DB::commit();
            return $this->successResponse('Customer Update Successfully.', $customer);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->errorResponse('Customer Update Failed.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function getUserDataFormRequest(array $data): array
    {
        $userData = [];
        if (isset($data['name_ja'])) {
            $userData['name_ja'] = $data['name_ja'];
        }

        return $userData;
    }

    public function destroy($id)
    {
        $customer = Customer::where('id', $id)->first();

        if (empty($customer)) {
            return $this->errorResponse('Customer Not Found.', 404);
        }

        // Fetch the associated user
        $user = User::where('id', $customer->user_id)->first();

        if (!empty($user)) {
            // Generate a unique postfix for the email
            $originalEmail = $user->email;
            $emailParts = explode('@', $originalEmail);
            $newEmail = $emailParts[0] . '-del-' . uniqid() . '@' . $emailParts[1];

            // Update the email address
            $user->update(['email' => $newEmail]);
        }

        // Soft delete the customer
        $customer->delete();

        return $this->successResponse('Customer deleted successfully. Email updated for uniqueness.');
    }

    public function homePage(Request $request)

    {
        $limit = $request->get('limit', 100);
        $page = $request->get('page', 1);
        $response = [];
        if (!empty($request->new_cast)) {
            $response = (new CastAction())->execute(['type' => 'new_cast', 'value' => null,'limit' => $limit, 'page' => $page]);
        } elseif (!empty($request->age)) {
            $response = (new CastAction())->execute(['type' => 'age', 'value' => $request->age,'limit' => $limit, 'page' => $page]);

        } elseif (!empty($request->popular_cast)) {
            $response = (new CastAction())->execute(['type' => 'popular_cast', 'value' => null,'limit' => $limit, 'page' => $page]);

        } elseif (!empty($request->available_cast)) {
            $response = (new CastAction())->execute(['type' => 'available_cast', 'value' => null,'limit' => $limit, 'page' => $page]);

        } else {
            $response[] = (new CastAction())->execute(['type' => 'new_cast', 'value' => null,'limit' => $limit, 'page' => $page]);
            $response[] = (new CastAction())->execute(['type' => 'popular_cast', 'value' => null,'limit' => $limit, 'page' => $page]);
        }


        return $this->successResponse('Casts retrieved successfully.', $response);
    }

    public function getFavCasts(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $page = $request->get('page', 1);
            $user = Auth::user();
            $customer = Customer::where('user_id', $user->id)->first();

            if (empty($customer)) {
                return $this->errorResponse('Customer Not Found.', 404);
            }

            $favCasts = !empty($customer->fav_cast_ids) ? json_decode($customer->fav_cast_ids, true) : [];

            $query = Cast::query();
            $query->whereIn('id', $favCasts);

            $casts = $query->paginate($limit, ['*'], 'page', $page);



            $response = [
                'casts' => CastResource::collection($casts),
                'total' => $casts->total(),
                'pages' => $casts->lastPage(),
            ];

            return $this->successResponse('Favorite Casts retrieved successfully.', $response);
        } catch (\Throwable $exception) {

            return $this->errorResponse('Favorite Casts retrieved Failed', 500, 'error', $exception->getMessage());
        }
    }

    public function addFavCast(AddFavCastRequest $request)
    {
        try {
            $user = Auth::user();
            $customer = Customer::where('user_id', $user->id)->first();

            if (empty($customer)) {
                return $this->errorResponse('Customer Not Found.', 404);
            }

            $favCasts = !empty($customer->fav_cast_ids) ? json_decode($customer->fav_cast_ids, true) : [];

            $favCasts[] = $request->get('cast_id');
            $favCasts = array_unique($favCasts);

            $customer->update(['fav_cast_ids' => json_encode($favCasts)]);

            return $this->successResponse('Favorite Casts added successfully.', []);

        } catch (\Throwable $exception) {
            return $this->errorResponse('Add Favorite Cast Failed.', 500, ['error' => $exception->getMessage()]);
        }
    }

    public function removeFavCast(AddFavCastRequest $request)
    {
        try {
            $user = Auth::user();
            $customer = Customer::where('user_id', $user->id)->first();

            if (empty($customer)) {
                return $this->errorResponse('Customer Not Found.', 404);
            }

            $favCasts = !empty($customer->fav_cast_ids) ? json_decode($customer->fav_cast_ids, true) : [];
            $favCasts = array_filter($favCasts, function ($value) use ($request) {
                return $value != $request->get('cast_id');
            });

            $favCasts = array_values($favCasts);
            $customer->update(['fav_cast_ids' => json_encode($favCasts)]);

            return $this->successResponse('Favorite Casts removed successfully.', []);

        } catch (\Throwable $exception) {

            return $this->errorResponse('Add Favorite removed Failed.', 500, ['error' => $exception->getMessage()]);
        }
    }
}
