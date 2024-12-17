<?php

namespace App\Http\Controllers\API\Payment\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Models\Customer\Customer;
use App\Models\Payment\Subscription;
use App\Models\Payment\SubscriptionHistory;
use App\Models\Payment\Transaction;
use App\Models\PointHistory;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;
use Stripe\Webhook;

class SubscriptionController extends Controller
{
    use APIResponse;

    public function createSubscription(CreateSubscriptionRequest $request)
    {
        $validated = $request->all();


        try {
            $user = $request->user();

            Stripe::setApiKey(config('services.stripe.secret'));

            if (!isset($user) || !isset($user->stripe_customer_id)) {
                return $this->errorResponse('User Not Found Registered For Stripe.', 400);
            }
            $checkIfAlreadySubscribed = $this->checkSubscription($user->id);

            if ($checkIfAlreadySubscribed) {
                return $this->errorResponse('User Already Subscribed.', 409, ['error' => '既に登録済みです']);
            }

            $subscription = StripeSubscription::create([
                'customer' => $user->stripe_customer_id,
                'items' => [['price' => $validated['plan_id']]],
            ]);


            $dbSubscription = Subscription::create([
                'user_id' => $user->id,
                'stripe_subscription_id' => $subscription->id,
                'stripe_customer_id' => $user->stripe_customer_id,
                'plan_id' => $validated['plan_id'],
                'status' => $subscription->status,
                'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
            ]);

            Transaction::create([
                'stripe_payment_id' => $subscription->id,
                'customer_email' => $validated['email'],
                'amount' => ($validated['points'] * 1.2),
                'currency' => $validated['currency'],
                'status' => $subscription->status,
                'description' => $validated['description'] ?? null,
                'payment_for' => 'subscriptions',
            ]);

            PointHistory::create([
                'user_id' => $user->id,
                'trx_id' => $subscription->id,
                'point_amount' => $validated['points'],
                'pay_amount' => $validated['points'] * 1.2,
                'payment_status' => 'Success',
                'sell_date_time' => now(),
            ]);

            $customer = Customer::where('user_id', $validated['user_id'])->first();

            $customer->update([
                'points_holded' => $customer->points_holded + $validated['points'],
            ]);

            Log::channel('subscription')->info(
                'Subscription Create successfully: '.json_encode($dbSubscription->toArray())
            );

            return $this->successResponse('Subscription created successfully.', $dbSubscription);
        } catch (\Throwable $e) {
            Log::channel('subscription')->error(
                'Subscription Failed : '.json_encode($validated),
                ['error' => $e->getMessage()]
            );

            return $this->errorResponse('Subscription Failed.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function fetchSubscription(Request $request, $id)
    {
        $subscription = Subscription::where('stripe_subscription_id', $id)->first();
        if (!$subscription) {
            return response()->json(['success' => false, 'message' => 'Subscription not found'], 404);
        }

        return $this->successResponse('Subscription fetched successfully.', $subscription->toArray());
    }

    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();

        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );

            // Handle the event
            switch ($event->type) {
                case 'customer.subscription.updated':
                    $this->updateSubscription($event->data->object);
                    break;

                case 'customer.subscription.deleted':
                    $this->cancelSubscription($event->data->object);
                    break;

                default:
                    Log::info('Unhandled event type: '.$event->type);
            }

            return response()->json(['message' => 'Webhook handled'], 200);
        } catch (\UnexpectedValueException $e) {
            Log::channel('subscription')->error(
                'Webhook Failed : '.$payload,
                ['error' => $e->getMessage()]
            );

            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException|\Exception $e) {
            Log::channel('subscription')->error(
                'Webhook Failed  signature verify failed: '.$payload,
                ['error' => $e->getMessage()]
            );

            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }

    private function updateSubscription($subscription)
    {
        $dbSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();

        if ($dbSubscription) {
            // Log the current state into the history table
            SubscriptionHistory::create([
                'subscription_id' => $dbSubscription->id,
                'stripe_subscription_id' => $dbSubscription->stripe_subscription_id,
                'stripe_customer_id' => $dbSubscription->stripe_customer_id,
                'plan_id' => $dbSubscription->plan_id,
                'status' => $dbSubscription->status,
                'current_period_start' => $dbSubscription->current_period_start,
                'current_period_end' => $dbSubscription->current_period_end,
            ]);

            // Update the current subscription record
            $dbSubscription->update([
                'status' => $subscription->status,
                'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
            ]);

            Log::info('Subscription updated: '.$subscription->id);
        }
    }

    private function cancelSubscription($subscription)
    {
        $dbSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();

        if ($dbSubscription) {
            // Log the current state into the history table
            SubscriptionHistory::create([
                'subscription_id' => $dbSubscription->id,
                'stripe_subscription_id' => $dbSubscription->stripe_subscription_id,
                'stripe_customer_id' => $dbSubscription->stripe_customer_id,
                'plan_id' => $dbSubscription->plan_id,
                'status' => $dbSubscription->status,
                'current_period_start' => $dbSubscription->current_period_start,
                'current_period_end' => $dbSubscription->current_period_end,
            ]);

            // Mark the subscription as canceled
            $dbSubscription->update([
                'status' => 'canceled',
            ]);

            Log::info('Subscription canceled: '.$subscription->id);
        }
    }

    public function fetchUserSubscriptions(Request $request, $userId)
    {
        // Fetch the current subscription
        $currentSubscription = Subscription::where('user_id', $userId)->first();

        // Fetch the subscription history
        $subscriptionHistory = SubscriptionHistory::where('subscription_id', $currentSubscription->id ?? null)
            ->orderBy('created_at', 'desc')
            ->get();

        if (!$currentSubscription && $subscriptionHistory->isEmpty()) {
            return $this->errorResponse('No subscriptions found for the user.', 404);
        }

        return $this->successResponse('Subscriptions fetched successfully.', [
            'current_subscription' => $currentSubscription,
            'subscription_history' => $subscriptionHistory,
        ]);
    }

    private function checkSubscription(mixed $user_id)
    {
        return Subscription::where('user_id', $user_id)->first();
    }


}
