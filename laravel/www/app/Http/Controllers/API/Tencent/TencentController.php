<?php

namespace App\Http\Controllers\API\Tencent;

use App\Actions\CreateUserForChatAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TencentCreateUserRequest;
use App\Http\Requests\TencentGenerateNewTokenRequest;
use App\Http\Requests\TencentUpdateUserRequest;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class TencentController extends Controller
{
    use APIResponse;
    public function createNewUser(TencentCreateUserRequest $request)
    {

    }

    public function updateUser(TencentUpdateUserRequest $request)
    {
        $data = $request->all();

        if (!isset($data['nickname']) && !isset($data['avatar'])) {
            return $this->errorResponse('No value provided', 400);
        }

        $result  = (new CreateUserForChatAction())->updateUser($data);

        if (isset($result['ActionStatus']) && $result['ActionStatus'] == 'OK') {
            return $this->successResponse([], 'User Data Update successfully.');
        }

        return $this->errorResponse('User ata Update Failed!', 400, ['error' => $result['ErrorInfo']]);
    }

    public function generateNewToken(TencentGenerateNewTokenRequest $request)
    {

    }

    public function sendMessageToUser(Request $request)
    {
        $result  = (new CreateUserForChatAction())->sendMessageToUser($request->user_id, $request->message);

        return $this->successResponse([], 'Message Sent successfully.');
    }
}
