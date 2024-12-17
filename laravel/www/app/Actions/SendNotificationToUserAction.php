<?php

namespace App\Actions;

use App\DTOs\CustomNotificationDTO;
use App\Http\Resources\CastResource;
use App\Models\Cast\Cast;
use App\Models\Customer\Customer;
use App\Models\Order\RequestMatching;
use App\Models\Order\RequestMatchingDetail;
use app\Services\PushNotificationService;
use Illuminate\Support\Facades\Log;

class SendNotificationToUserAction
{
    public static function sendNotificationToCastFormRequestMatchingDetails($requestMatchingDetails)
    {
        try {
            foreach ($requestMatchingDetails as $data) {
                $cast = Cast::where('id', $data['cast_id'])->first();
                if ($cast) {
                    PushNotificationService::sendCustomNotification(
                        $cast->user,
                        new CustomNotificationDTO(
                            'Proposal Confirmed: Required Participants Have Joined',
                            'The required number of participants have joined the call. The proposal has been confirmed.',
                            null
                        )
                    );
                }
            }
            Log::channel('notification')->info('Send Notification to Cast FormRequest Matching Details');
        } catch (\Throwable $th) {
            Log::channel('notification')->error('Failed to send Notification to Cast FormRequest Matching Details', ['error' => $th->getMessage()]);
        }
    }

    public static function sendNotificationToCustomerFromRequestMatching($requestMatchingData)
    {
        try {
            $customer = Customer::where('id', $requestMatchingData['customer_id'])->first();
            if ($customer) {
                PushNotificationService::sendCustomNotification(
                    $customer->user,
                    new CustomNotificationDTO(
                        'Proposal Confirmed: Required Participants Have Joined',
                        'The required number of participants have joined the call. The proposal has been confirmed.',
                        null
                    )
                );
            }
            Log::channel('notification')->info('Send Notification to Customer FormRequest Matching Details');
        } catch (\Throwable $th) {
            Log::channel('notification')->error('Failed to send Notification to Customer FormRequest Matching Details', ['error' => $th->getMessage()]);
        }
    }

    public static function sendBeforeMeetingNotification($type, $users, $message = [])
    {
        try {
            if ($type == 'cast') {
                foreach ($users as $data) {
                    $cast = Cast::where('id', $data['cast_id'])->first();
                    if ($cast) {
                        PushNotificationService::sendCustomNotification(
                            $cast->user,
                            new CustomNotificationDTO(
                                $message['title'],
                                $message['body'],
                                null
                            )
                        );
                        Log::channel('notification')->info('Send Notification Meeting reminder notification.');
                    }
                }
            }

            elseif ($type == 'customer') {
                foreach ($users as $data) {
                    $customer = Customer::where('id', $data['customer_id'])->first();
                    if ($customer) {
                        PushNotificationService::sendCustomNotification(
                            $customer->user,
                            new CustomNotificationDTO(
                                $message['title'],
                                $message['body'],
                                null
                            )
                        );
                        Log::channel('notification')->info('Send Notification Meeting reminder notification.');
                    }
                }
            }

        } catch (\Throwable $th) {
            Log::channel('notification')->error('Failed to send Notification to Cast ', ['error' => $th->getMessage()]);
        }
    }

    public static function sendCustomerNotification($user, $message = [])
    {
        try {
            PushNotificationService::sendCustomNotification(
            $user->user,
                new CustomNotificationDTO(
                    $message['title'],
                    $message['body'],
                    null
                )
            );

            Log::channel('notification')->info('Send Notification Disband notification.');

        } catch (\Throwable $th) {
            Log::channel('notification')->error('Failed to send Notification to Cast ', ['error' => $th->getMessage()]);
        }
    }
}
