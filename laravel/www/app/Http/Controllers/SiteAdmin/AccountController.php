<?php

namespace App\Http\Controllers\SiteAdmin;

use App\Http\Controllers\Controller;
use App\Models\AccountList;
use App\Models\UserList;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    use APIResponse;

    public function register(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'ac-name_ja' => 'required|string|max:255',
            'us-name_ja' => 'required|string|max:255',
            'ac-email' => 'string|email|max:255',
            'us-email' => 'required|email|max:255',
            'password' => 'required|min:14',
            'phone' => 'numeric',
            'chef_admin' => 'string',
            'post_code' => 'string',
            'status' => 'required|string',
            'prefectures' => 'string',
            'municipalities' => 'string',
            'address_1' => 'string',
            'address_2' => 'string',
            'user_type' => 'required|string',
            'account_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $new_account = AccountList::create([
            'name_ja' => $request->input('ac-name_ja'),
            'email' => $request->input('ac-email'),
            'phone' => $request->input('phone'),
            'chef_admin' => $request->input('chef_admin'),
            'post_code' => $request->input('post_code'),
            'prefectures' => $request->input('prefectures'),
            'municipalities' => $request->input('municipalities'),
            'address_1' => $request->input('address_1'),
            'address_2' => $request->input('address_2'),

        ]);

        if ($new_account) {
            $user = UserList::create([
                'name_ja' => $request->input('us-name_ja'),
                'email' => $request->input('us-email'),
                'status' => $request->input('status'),
                'account_id' => $new_account->id,
                'account_type' => $request->input('account_type'),
                'user_type' => $request->input('user_type'),
                'password' => Hash::make($request->input('password')),
            ]);

            if ($user) {
                $data = [
                    'account' => $new_account,
                    'user' => $user,
                ];

                return $this->successResponseWithData($data, 'Account created successfully');
            } else {
                return $this->failureResponseWithMessage('Account created but user creation failed', 422);
            }
        } else {
            return $this->failureResponseWithMessage('Account creation failed', 422);
        }
    }

    public function accountSettings()
    {
        $account_details = UserList::where('id', '=', Auth::id())
            ->with('account')
            ->first();

        return $this->successResponseWithData($account_details, 'Account settings loaded successfully');
    }

    public function updateAccount(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name_ja' => 'required|string|max:255',
            'email' => 'email|unique:user_lists,email,'.Auth::id(),
            'phone' => 'numeric|min:9|max:11',
            'chef_admin' => 'string',
            'post_code' => 'string',
            'prefectures' => 'string',
            'municipalities' => 'string',
            'address_1' => 'string',
            'address_2' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }
        $account_update = AccountList::where('id', '=', UserList::find(Auth::id())->account->id)
            ->update([
                'name_ja' => $request->input('name_ja'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'chef_admin' => $request->input('chef_admin'),
                'post_code' => $request->input('post_code'),
                'prefectures' => $request->input('prefectures'),
                'municipalities' => $request->input('municipalities'),
                'address_1' => $request->input('address_1'),
                'address_2' => $request->input('address_2'),
            ]);

        if ($account_update) {
            return $this->successResponseWithMessage('Account updated successfully');
        }

        return $this->failureResponseWithMessage('Account update failed', 422);
    }
}
