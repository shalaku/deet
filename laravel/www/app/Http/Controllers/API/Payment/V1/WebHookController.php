<?php

namespace App\Http\Controllers\API\Payment\V1;

use App\Http\Controllers\Controller;
use App\Traits\APIResponse;
use App\Models\Customer\Customer;
use App\Models\Payment\Transaction;
use App\Models\PointHistory;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Payment\Subscription;
use App\Models\Payment\SubscriptionHistory;
use Stripe\Exception\SignatureVerificationException;

class WebHookController extends Controller
{
    use APIResponse;

    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();

        Log::channel('subscription')->info('Webhook Incoming:', ['content' => $payload]);

        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.subscription_webhook_secret');

//        Log::channel('subscription')->info(
//            'Transaction Webhook keys: ',
//            ['webhook' => $sigHeader, 'webhook_secret' => $webhookSecret]
//        );

        try {
            // Verify the Stripe event
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            Log::channel('subscription')->info('Transaction Webhook Response:'.json_encode($event));

            // Handle the event based on its type
            switch ($event->type) {
                case 'customer.subscription.updated':
                    // Handle subscription updated events
                    $this->updateSubscription($event->data->object);
                    break;

                case 'customer.subscription.deleted':
                    // Handle subscription deleted events
                    $this->cancelSubscription($event->data->object);
                    break;

                default:
                    Log::channel('subscription')->info("default");

                    Log::info('Unhandled event type: '.$event->type);
            }

            return response()->json(['message' => 'Webhook handled'], 200);
        } catch (\UnexpectedValueException $e) {
            Log::channel('transaction')->info('Transaction Webhook Request:'.json_encode($request->all()));
            Log::channel('transaction')->error('Transaction Webhook Error: ', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException|\Exception $e) {
            Log::channel('transaction')->info('Transaction Webhook Request:'.json_encode($request->all()));
            Log::channel('transaction')->error('Transaction Webhook Error: ', ['error' => $e->getMessage()]);


            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }


    private function processAddPoints($charge)
    {
        $pointHistory = PointHistory::where('trx_id', $charge->payment_intent)->first();
        $customer = Customer::where('user_id', $pointHistory->user_id)->first();

        Log::channel('transaction')->info('Processing add points for customer: '.json_encode($customer));

        $customer->update([
            'points_holded' => $customer->points_holded + $pointHistory->point_amount,
        ]);

        $pointHistory->update(['payment_status' => 'Success']);
    }

//    private function updateSubscription($subscription)
//    {
//        $dbSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();
//
//        if ($dbSubscription) {
//            SubscriptionHistory::create([
//                'subscription_id' => $dbSubscription->id,
//                'stripe_subscription_id' => $dbSubscription->stripe_subscription_id,
//                'stripe_customer_id' => $dbSubscription->stripe_customer_id,
//                'plan_id' => $dbSubscription->plan_id,
//                'status' => $dbSubscription->status,
//                'current_period_start' => $dbSubscription->current_period_start,
//                'current_period_end' => $dbSubscription->current_period_end,
//            ]);
//
//            $dbSubscription->update([
//                'status' => $subscription->status,
//                'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
//                'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
//            ]);
//
//            Transaction::create([
//                'stripe_payment_id' => $subscription->id,
////                'customer_email' => $validated['email'],
//                'amount' => $subscription->items->data[0]->price->unit_amount,
//                'currency' => $subscription->items->data[0]->price->unit_amount,
//                'status' => $subscription->status,
////                'description' => $request->description,
//                'payment_for' => 'subscriptions',
//            ]);
//
//            $user = User::where('stripe_customer_id', $subscription->customer)->first();
//            // stripe customer id
////            if (!isset($request->payment_for) || $request->payment_for == 'add_points') {
//            PointHistory::create([
//                'user_id' => $user->id,
//                'trx_id' => $subscription->id,
//                'point_amount' => ($subscription->items->data[0]->price->unit_amount / 1.2),
//                'pay_amount' => $subscription->items->data[0]->price->unit_amount,
//                'payment_status' => 'Success',
//                'sell_date_time' => now(),
//            ]);
////            }
//            $customer = Customer::where('user_id', $user->id)->first();
//
//            $customer->update([
//                'points_holded' => $customer->points_holded + ($subscription->items->data[0]->price->unit_amount / 1.2),
//            ]);
//
//            Log::channel('transaction')->info('Subscription updated: '.$subscription->id);
//        }
//
//        Log::channel('subscription')->info('Subscription updated1: '.json_encode($subscription));
//    }
    private function updateSubscription($subscription)
    {
        // Retrieve the subscription record from the database
        $dbSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();

        if (!$dbSubscription) {
            Log::channel('subscription')->warning('No subscription found for Stripe ID: '.$subscription->id);
//            $user = User::where('stripe_customer_id', $subscription->customer)->first();
//            if ($user) {
            $dbSubscription = Subscription::create([
                'user_id' => $subscription->customer,
                'stripe_subscription_id' => $subscription->id,
                'stripe_customer_id' => $subscription->customer,
                'plan_id' => $subscription->items->data[0]->plan->id,
                'status' => $subscription->status,
                'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
            ]);
            Log::channel('subscription')->info('Updating subscription: '.$subscription->customer);
//            }

            Log::channel('subscription')->info('User and DB Not Found: ');

            return;
        }

        try {
            // Log subscription before update
            Log::channel('subscription')->info('Creating subscription: '.$dbSubscription->id);

            // Record subscription history
            SubscriptionHistory::create([
                'subscription_id' => $dbSubscription->id,
                'stripe_subscription_id' => $dbSubscription->stripe_subscription_id,
                'stripe_customer_id' => $dbSubscription->stripe_customer_id,
                'plan_id' => $dbSubscription->plan_id,
                'status' => $dbSubscription->status,
                'current_period_start' => $dbSubscription->current_period_start,
                'current_period_end' => $dbSubscription->current_period_end,
            ]);

            // Update the subscription details
            $dbSubscription->update([
                'status' => $subscription->status,
                'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
            ]);

            // Record transaction
            $transactionData = [
                'stripe_payment_id' => $subscription->id,
                'amount' => $subscription->items->data[0]->price->unit_amount,
                'currency' => $subscription->items->data[0]->price->currency,
                'status' => $subscription->status,
                'payment_for' => 'subscriptions',
            ];
            Transaction::create($transactionData);

            // Log transaction
            Log::channel('transaction')->info('Transaction recorded: ', $transactionData);

            // Retrieve user and process points
            $user = User::where('stripe_customer_id', $subscription->customer)->first();
            if ($user) {
                $pointAmount = $subscription->items->data[0]->price->unit_amount / 1.2;

                PointHistory::create([
                    'user_id' => $user->id,
                    'trx_id' => $subscription->id,
                    'point_amount' => $pointAmount,
                    'pay_amount' => $subscription->items->data[0]->price->unit_amount,
                    'payment_status' => 'Success',
                    'sell_date_time' => now(),
                ]);

                // Update customer's points
                $customer = Customer::where('user_id', $user->id)->first();
                if ($customer) {
                    $customer->update([
                        'points_holded' => $customer->points_holded + $pointAmount,
                    ]);

                    // Log customer points update
                    Log::channel('transaction')->info(
                        'Customer points updated for User ID: '.$user->id.', Points Added: '.$pointAmount
                    );
                } else {
                    Log::channel('subscription')->warning('No customer found for User ID: '.$user->id);
                }
            } else {
                Log::channel('subscription')->warning('No user found for Stripe Customer ID: '.$subscription->customer);
            }

            // Final subscription update log
            Log::channel('subscription')->info('Subscription successfully updated: '.$subscription->id);
        } catch (\Exception $e) {
            Log::channel('error')->error('Error updating subscription: '.$subscription->id.' - '.$e->getMessage());
        }
    }

    private function cancelSubscription($subscription)
    {
        $dbSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();

        if ($dbSubscription) {
            SubscriptionHistory::create([
                'subscription_id' => $dbSubscription->id,
                'stripe_subscription_id' => $dbSubscription->stripe_subscription_id,
                'stripe_customer_id' => $dbSubscription->stripe_customer_id,
                'plan_id' => $dbSubscription->plan_id,
                'status' => $dbSubscription->status,
                'current_period_start' => $dbSubscription->current_period_start,
                'current_period_end' => $dbSubscription->current_period_end,
            ]);

            $dbSubscription->update(['status' => 'canceled']);
            Log::info('Subscription canceled: '.$subscription->id);
        }
    }
}
