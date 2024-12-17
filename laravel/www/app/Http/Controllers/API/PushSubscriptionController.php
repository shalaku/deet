<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationBroadcastRequest;
use App\Http\Requests\PushSubscriptionRequest;
use App\Http\Requests\PushUnsubscribeRequest;
use App\Models\User;
use App\Notifications\PushNotification;
use App\Traits\APIResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushSubscriptionController extends Controller
{
    use APIResponse;
    public function subscribe(PushSubscriptionRequest $request)
    {
        try {
            $user = Auth::user();
            Log::info('Push Subscription Subscribe', [
                $request->post('endpoint'),
                $request->post('public_key'),
                $request->post('auth_token'),
                $request->post('content_encoding')
            ]);

            // Update or create the push subscription
            $user->updatePushSubscription(
                $request->post('endpoint'),
                $request->post('public_key'),
                $request->post('auth_token'),
                $request->post('content_encoding') ?? 'aesgcm'
            );



            return $this->successResponse('Subscription created successfully.');

        } catch (\Throwable $th) {
            return $this->errorResponse('Subscription creation failed', 400, ['error' => $th->getMessage()]);
        }

    }

    public function unsubscribe(PushUnsubscribeRequest $request)
    {
        $user = Auth::user();

        $user->deletePushSubscription($request->post('endpoint'));

        return $this->successResponse('Unsubscribed successfully.');
    }

//    public function broadcast(NotificationBroadcastRequest $request)
//    {
//        try {
//            $user = Auth::user();
//
//            if ($user->account_type != 'admin') {
//                return $this->errorResponse('You do not have permission to make this request', 403);
//            }
//
//
//            $users = User::with('pushSubscriptions')->get();
//
//            foreach ($users as $user) {
//                if ($user->pushSubscriptions()->count()) {
//                    $user->notify(new PushNotification(
//                        $request->post('title'),
//                        $request->post('body'),
//                        $request->post('icon')
//                    ));
//                }
//            }
//
//            return $this->successResponse('Your request is progress.');
//        } catch (\Throwable $th) {
//            dd($th->getMessage());
//        }
//    }
    public function broadcast(NotificationBroadcastRequest $request)
    {
        try {
            $user = Auth::user();

            if ($user->account_type != 'admin') {
                return $this->errorResponse('You do not have permission to make this request', 403);
            }

            $users = User::with('pushSubscriptions')->get();

            foreach ($users as $user) {
                $subscriptions = $user->pushSubscriptions;

                foreach ($subscriptions as $subscription) {
                    Log::info('Push Subscription', [
                        'endpoint' => $subscription->endpoint,
                        'public_key' => $subscription->public_key,
                        'auth_token' => $subscription->auth_token,
                        'content_encoding' => $subscription->content_encoding,
                    ]);

                    if (!$this->isValidBase64($subscription->public_key)) {
                        Log::warning('Invalid public_key for user ' . $user->id, [
                            'public_key' => $subscription->public_key
                        ]);
                        continue;
                    }
                    // Validate auth_token and public_key
//                    if (!$this->isValidBase64($subscription->auth_token)) {
//                        Log::error('Invalid auth_token for user ' . $user->id, ['auth_token' => $subscription->auth_token]);
//                        continue;
//                    }
//
//                    if (!$this->isValidBase64($subscription->public_key)) {
//                        Log::error('Invalid public_key for user ' . $user->id, ['public_key' => $subscription->public_key]);
//                        continue;
//                    }


                    // Proceed with sending the notification if valid
                    $user->notify(
                        new PushNotification(
                            $request->post('title'),
                            $request->post('body'),
                            $request->post('icon')
                        )
                    );
                }
            }

            return $this->successResponse('Your request is in progress.');
        } catch (\Throwable $th) {
            // Log the exception with detailed error information
            Log::error('Error in broadcasting push notifications', [
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ]);

            return $this->errorResponse('Broadcast failed', 500, ['error' => $th->getMessage()]);
        }
    }

    private function isValidBase64($string)
    {
        // Check if the string is a valid Base64 encoded string
        $decoded = base64_decode($string, true);
        return $decoded !== false && base64_encode($decoded) === $string;
    }


}
