<?php

namespace App\Http\Controllers\API;

use App\Actions\CreateUserForChatAction;
use App\Helpers\TencentHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordSendEmialRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UserRequest;
use App\Mail\ForgotPasswordMail;
use App\Models\MailLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\RefreshToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Traits\APIResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;



class AuthController extends Controller
{
    use APIResponse;
    public function register(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['password'] = Hash::make($request->password);
            $data['uuid'] = (string) Str::uuid();

            $user = User::create($data);

            $token = $this->createAccessToken($user);
            $refreshToken = $this->createRefreshToken($user, $token);
            $responseData = $this->respondWithToken($token, $refreshToken);
            $responseData['user'] = $this->formatProfileData($user);

            $chatUser = (new CreateUserForChatAction())->createChatUser('user_' . $user->id);

            $user->update([
                'user_sign' => $chatUser->userSig,
                'user_id' => $chatUser->userId
            ]);

            DB::commit();
            return $this->successResponse('user registered successfully', $responseData);
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return $this->errorResponse('user registered Failed!', 400);
        }
    }

    /**
     * Login and create access token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation Failed!', 422, $validator);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->errorResponse('Unauthorized', 401, ['error' => 'Invalid credentials']);
        }

        $user = Auth::user();
        $token = $this->createAccessToken($user);
        $refreshToken = $this->createRefreshToken($user, $token);
        $responseData = $this->respondWithToken($token, $refreshToken);
        $responseData['user'] = $this->formatProfileData($user);

        return $this->successResponse('Login Successfully.', $responseData);
    }

    /**
     * Refresh the JWT token.
     */
    public function refresh(Request $request)
    {
        try {
            $refreshToken = $request->header('refresh_token');

            if (!$refreshToken) {
                return $this->errorResponse('Refresh token is required!', 422, ['refresh_token' => 'Refresh token is required!']);
            }

            $storedToken = RefreshToken::where('token', $refreshToken)->first();

            if (!$storedToken) {
                return $this->errorResponse('Unauthorized', 401, ['error' => 'Invalid Refresh token']);
            }

            $user = $storedToken->user;

            $token = $this->createAccessToken($user);

            $storedToken->delete();
            $newRefreshToken = $this->createRefreshToken($user, $token);
            $responseData = $this->respondWithToken($token, $newRefreshToken);
            $responseData['user'] = $this->formatProfileData($user);

            return $this->successResponse('Token refreshed successfully.', $responseData);
        } catch (\Throwable $throwable) {
            return $this->errorResponse('Could not refresh token..', 500, ['error' => $throwable->getMessage()]);

        }

    }

    /**
     * Logout from current device.
     */
    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $payload = JWTAuth::setToken($token)->getPayload();
            $jti = $payload->get('jti');

            JWTAuth::invalidate($token);
            RefreshToken::where('jti', $jti)->delete();

            return $this->successResponse('Logout Successfully.');
        } catch (JWTException $e) {

            return $this->errorResponse('Failed to logout, please try again', 500, ['error' => 'Failed to logout, please try again']);
        }
    }

    /**
     * Logout from all devices.
     */
    public function logoutAllDevices()
    {
        try {
            $user = Auth::user();

            $user->token_version += 1;
            $user->save();

            $user->refreshTokens()->delete();

            return $this->successResponse('Logout Successfully.', ['message' => 'Successfully logged out from all devices']);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to logout, please try again later', 500, ['error' => 'Failed to logout, please try again later']);
        }
    }

    protected function respondWithToken($token, $refreshToken)
    {
        return [
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => auth()->factory()->getTTL() * 60, // seconds
            'refresh_token' => $refreshToken,
        ];
    }

    /**
     * Create a new refresh token.
     */
    protected function createRefreshToken($user, $token)
    {
        $refreshToken = Str::random(80);
        $payload = JWTAuth::setToken($token)->getPayload();
        $jti = $payload->get('jti');


        RefreshToken::create([
            'user_id' => $user->id,
            'token'   => $refreshToken,
            'jti'     => $jti,
        ]);

        return $refreshToken;
    }

    private function createAccessToken($user = null)
    {
        $user = $user == null ? Auth::getUser() : $user;
        $customClaims = [
            'id' => $user->uuid,
            'account_type' => $user->account_type,
            'user_type' => $user->user_type,
            'token_version' => $user->token_version ?? 0,
        ];

        return  JWTAuth::claims($customClaims)->fromUser($user);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();
        $data = $this->formatProfileData($user);

        return $this->successResponse('User profile', $data);
    }

    private function formatProfileData($user)
    {
        $data =  [
            'id' => $user->id,
            'name_ja' => $user->name_ja,
            'email' => $user->email,
            'account_type' => $user->account_type,
            'user_type' => $user->user_type,
            'stripe_customer_id' => $user->stripe_customer_id
        ];

        if ($user->account_type == 'admin') {
            $data['user_id'] = config('tencent.chat.manager_user');
            $data['user_sig'] = TencentHelper::generateUserSig(config('tencent.chat.manager_user'));
            $data['sdkAppId'] = config('tencent.chat.sdkAppId');
        }

        return $data;
    }

    public function sendEmail(ForgotPasswordSendEmialRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $token = random_int(100000, 999999);
            Mail::to($request->email)->send(new ForgotPasswordMail($token));
            MailLog::create([
                'email' => $request->email,
                'token' => Hash::make($token),
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            return $this->successResponse('Mail Send Successfully.');

        } catch (\Throwable $throwable) {
            return $this->errorResponse('Mail Send Failed!', 500, ['error' => $throwable->getMessage()]);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            $mailData = MailLog::where('email', $user->email)
                ->where('revoked', 0)
                ->latest('created_at')
                ->first();

            if ($mailData && Hash::check($data['token'], $mailData['token'])) {
                $password = Hash::make($data['password']);
                $user->update([
                    'password' => $password,
                ]);
                $mailData->update([
                    'revoked' => 1,
                ]);

                DB::commit();

                return $this->successResponse('Password Reset Successfully.');
            }

            return $this->errorResponse('Invalid token.', 401, ['error' => 'Invalid token']);
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return $this->errorResponse('Password Reset Failed!', 500, ['error' => $throwable->getMessage()]);
        }
    }
}
