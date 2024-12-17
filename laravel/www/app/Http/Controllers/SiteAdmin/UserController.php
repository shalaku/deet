<?php

namespace App\Http\Controllers\SiteAdmin;

use App\Http\Controllers\Controller;
use App\Models\AccountList;
use App\Models\UserList;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use APIResponse;
    //    private $account=[];
    //
    //    public function __construct()
    //    {
    //        $this->account=AccountList::where('id','=',UserList::find(Auth::id())->account->id)->first();
    //    }

    public function getAccountInfoForUserRegistration()
    {
        $account = AccountList::where('id', '=', UserList::find(Auth::id())->account->id)->first();

        if ($account) {
            return $this->successResponseWithData($account);
        } else {
            return $this->failureResponse();
        }

    }

    public function registerUser(Request $request)
    {

        $validator = validator()->make($request->all(), [
            'name_ja' => 'required',
            'email' => 'required|email|unique:user_lists',
            'password' => 'required|min:14',
            //'password_confirmation'=>'required',
            'status' => 'required|in:active,freeze',
            'account_id' => 'required|exists:account_lists,id',
            'account_type' => 'required|string',
            'user_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        //Restricting unauthorized user type entry

        $user_type = '-';

        if (strtolower($request->account_type) == 'store') {
            if ($request->input('user_type') == 'admin' || $request->input('user_type') == 'staff') {
                $user_type = strtolower($request->input('user_type'));
            } else {
                return $this->failureResponseWithMessage('Invalid user type!');
            }

        }

        //creating user
        $user = UserList::create([
            'name_ja' => $request->input('name_ja'),
            'email' => $request->input('email'),
            'account_id' => $request->input('account_id'),
            'account_type' => $request->input('account_type'),
            'user_type' => $user_type,
            'status' => $request->input('status'),
            'password' => Hash::make($request->input('password')),
        ]);

        if ($user) {
            return $this->successResponseWithData($user, 'User created successfully');
        }

        return $this->failureResponse(422);

    }

    public function getUserInfoForUserSettings()
    {
        $user = UserList::find(Auth::id())->with('account')->first();
        if ($user) {
            return $this->successResponseWithData($user);
        }

        return $this->failureResponse();
    }

    public function updateUserSettings(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name_ja' => 'required|string',
            'email' => 'required|email',
            //            'account_id'=>'required|exists:account_lists,id',
            //            'account_type'=>'required|string',
            //            'user_type'=>'required|string',

        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $updated_user = UserList::where('id', '=', Auth::id())->update([
            'name_ja' => $request->input('name_ja'),
            'email' => $request->input('email'),
        ]);

        if ($updated_user) {
            return $this->successResponseWithMessage('User updated successfully');
        }

        return $this->failureResponse();
    }

    public function getUserLists()
    {
        $users = UserList::all();

        if ($users->isEmpty()) {
            return $this->failureResponse();
        }

        return $this->successResponseWithData($users);
    }

    public function activateUser(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'id' => 'required|exists:user_lists,id',
            'status' => 'required|in:active,freeze',
        ]);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }
        $message = 'User activated successfully';

        if (strtolower($request->input('status')) == 'freeze') {
            $message = 'User freezed successfully';
        }

        $userStatusUpdate = UserList::where('id', '=', $request->input('id'))->update([
            'status' => $request->input('status'),
        ]);

        if ($userStatusUpdate) {
            return $this->successResponseWithMessage($message);

        }

        return $this->failureResponse();
    }
}
