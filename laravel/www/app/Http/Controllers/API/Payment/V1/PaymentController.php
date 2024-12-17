<?php

namespace App\Http\Controllers\API\Payment\V1;

use App\Http\Controllers\API\Customer\CustomerCategoryListController;
use App\Http\Controllers\Controller;
use App\Http\Requests\AutoPayRequest;
use App\Http\Requests\CreatePaymentRequest;
use App\Models\Customer\Customer;
use App\Models\Payment\PaymentAmountPoint;
use App\Models\Payment\Transaction;
use App\Models\PointHistory;
use App\Traits\APIResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use Stripe\Stripe;

class PaymentController extends Controller
{
    use APIResponse;

    public function createPaymentIntent(CreatePaymentRequest $request)
    {
        DB::beginTransaction();
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::create([
                'amount' => ($request->points * 1.2),
                'currency' => $request->currency,
                'description' => $request->description,
                'receipt_email' => $request->email,
                'metadata' => ['integration_check' => 'accept_a_payment'],
            ]);

            Transaction::create([
                'stripe_payment_id' => $paymentIntent->id,
                'customer_email' => $request->email,
                'amount' => ($request->points * 1.2),
                'currency' => $request->currency,
                'status' => $paymentIntent->status,
                'description' => $request->description,
                'payment_for' => $request->payment_for ?? 'add_points',
            ]);

            if (!isset($request->payment_for) || $request->payment_for == 'add_points') {
                PointHistory::create([
                    'user_id' => $request->user_id,
                    'trx_id' => $paymentIntent->id,
                    'point_amount' => $request->points,
                    'pay_amount' => $request->points * 1.2,
                    'payment_status' => 'Initiated',
                    'sell_date_time' => now(),
                ]);
            }

            DB::commit();

            return $this->successResponse(
                'Payment initiated successfully.',
                ['clientSecret' => $paymentIntent->client_secret]
            );
        } catch (\Throwable $e) {
            DB::rollback();

            return $this->errorResponse('Payment initiated Failed.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function webhook(Request $request)
    {
        $signature = $request->header('Stripe-Signature');
        $payload = $request->getContent();
        $webhookSecret = config('services.stripe.webhook_secret');

//        Log::channel('transaction')->info('Transaction Webhook secret:' . $webhookSecret);

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                $webhookSecret
            );

            Log::channel('transaction')->info('Transaction Webhook Response:'.json_encode($event));

            if ($event['type'] == 'charge.updated') {
//                Log::channel('transaction')->info('Inner Success Condition.');

                $transaction = Transaction::where(
                    'stripe_payment_id',
                    $event['data']['object']['payment_intent']
                )->first();
                Log::channel('transaction')->info('Transaction Data: '.json_encode($transaction));

                if ($transaction->payment_for == 'add_points') {
                    Log::channel('transaction')->info('Inner Add points.');

                    $pointHistory = PointHistory::where('trx_id', $event['data']['object']['payment_intent'])->first();
                    Log::channel('transaction')->info('Points History Data: '.json_encode($pointHistory));

                    $customer = Customer::where('user_id', $pointHistory->user_id)->first();
                    Log::channel('transaction')->info('Customer Data: '.json_encode($customer));

                    $customer->update([
                        'points_holded' => $customer->points_holded + $pointHistory->point_amount,
                    ]);

                    $pointHistory->update([
                        'payment_status' => "Success",
                    ]);
                }
                $transaction->update([
                    'status' => "Success",
                ]);
            } elseif ($event['type'] === 'payment_intent.payment_failed') {
                $paymentIntent = $event['data']['object'];

                $transaction = Transaction::where('stripe_payment_id', $paymentIntent['id'])->first();

                if ($transaction->payment_for == 'add_points') {
                    $pointHistory = PointHistory::where('order_id', $paymentIntent['id'])->first();
                    $pointHistory->update([
                        'payment_status' => "Failed",
                    ]);
                }

                $transaction->update([
                    'status' => "Failed",
                ]);
            }

            return $this->successResponse('Success');
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::channel('transaction')->info('Transaction Webhook Request:'.json_encode($request->all()));
            Log::channel('transaction')->error('Transaction Webhook Error: ', ['error' => $e->getMessage()]);

            return $this->errorResponse('Signature Verification Failed.', 500, ['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::channel('transaction')->info('Transaction Webhook Request:'.json_encode($request->all()));
            Log::channel('transaction')->error('Transaction Webhook Error: ', ['error' => $e->getMessage()]);

            return $this->errorResponse('Failed.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function getPaymentAmounts(Request $request)
    {
        try {
            $allData = PaymentAmountPoint::select('id', 'points')->get();

            return $this->successResponse('Points List.', $allData);
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function autoPay(AutoPayRequest $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $data = $request->all();

            $customer = \Stripe\Customer::retrieve($data['stripe_customer_id']);
            $defaultPaymentMethod = $customer->invoice_settings->default_payment_method;

            if (!$defaultPaymentMethod) {
                return $this->errorResponse('Customer does not have a default payment method');
            }

            $paymentIntent = PaymentIntent::create([
                'amount' => $data['amount'],
                'currency' => 'JPY',
                'customer' => $data['stripe_customer_id'],
                'payment_method' => $defaultPaymentMethod,
                'off_session' => true,
                'confirm' => true,
            ]);

            if ($paymentIntent->status === 'succeeded') {
                Log::channel('transaction')->info('Auto Payment Successfully Done: ' . json_encode($paymentIntent));

                return $this->successResponse('Payment successful!');
            }

            Log::channel('transaction')->error('Auto Payment Failed: ' . json_encode($paymentIntent));

            return $this->errorResponse('Payment failed!');
        } catch (\Throwable $e) {
            Log::channel('transaction')->error('Auto Payment Failed: ' . $e->getMessage());

            return $this->errorResponse('Payment failed!');
        }
    }
}
