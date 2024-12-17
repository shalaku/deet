<?php

namespace App\Http\Controllers\API\Payment\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CardSaveRequest;
use App\Models\User;
use App\Traits\APIResponse;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class CardController extends Controller
{
    use APIResponse;

    public function __construct()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function save(CardSaveRequest $request)
    {
        try {
            $validated = $request->all();
            $user = User::findOrFail($validated['userId']);

            if (empty($user->stripe_customer_id)) {
                $stripeCustomer = Customer::create([
                    'email' => $user->email,
                ]);

                $user->stripe_customer_id = $stripeCustomer->id;
                $user->save();
            } else {
                $stripeCustomer = Customer::retrieve($user->stripe_customer_id);
            }

            $paymentMethod = PaymentMethod::retrieve($validated['paymentMethodId']);
            $paymentMethod->attach(['customer' => $stripeCustomer->id]);

            $defaultPaymentMethodId = $stripeCustomer->invoice_settings->default_payment_method;

            if (empty($defaultPaymentMethodId)) {
                Customer::update($stripeCustomer->id, [
                    'invoice_settings' => ['default_payment_method' => $validated['paymentMethodId']],
                ]);
            }

            return $this->successResponse('Payment method saved', [], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Payment method not saved', 500, ['error' => $e->getMessage()]);
        }
    }

    public function makeDefaultCard(CardSaveRequest $request)
    {
        try {
            $validated = $request->all();
            $user = User::findOrFail($validated['userId']);

            $stripeCustomer = Customer::retrieve($user->stripe_customer_id);

            Customer::update($stripeCustomer->id, [
                'invoice_settings' => ['default_payment_method' => $validated['paymentMethodId']],
            ]);

            return $this->successResponse('Payment method saved', [], 201);

        } catch (\Throwable $th) {
            return $this->errorResponse('Payment method not saved', 500, ['error' => $th->getMessage()]);
        }
    }

    public function get($userId)
    {
        try {
            $user = User::findOrFail($userId);

            if (empty($user->stripe_customer_id)) {
                return $this->errorResponse('Stripe customer not found', 404);
            }

            $paymentMethods = \Stripe\PaymentMethod::all([
                'customer' => $user->stripe_customer_id,
                'type' => 'card',
            ]);

            $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);

            $defaultPaymentMethodId = $customer->invoice_settings->default_payment_method;
            $defaultCard = null;

            if ($defaultPaymentMethodId) {
                foreach ($paymentMethods->data as $paymentMethod) {
                    if ($paymentMethod->id === $defaultPaymentMethodId) {
                        $defaultCard = $paymentMethod;
                        break;
                    }
                }
            }

            return $this->successResponse('Payment methods retrieved', [
                'payment_methods' => $paymentMethods->data,
                'default_card' => $defaultCard,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Payment method not retrieved', 500, ['error' => $e->getMessage()]);
        }
    }



    public function delete($userId, $paymentMethodId)
    {
        try {
            $user = User::findOrFail($userId);

            if (empty($user->stripe_customer_id)) {
                return $this->errorResponse('Stripe customer ID not found for this user.', 404);
            }

            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

            if ($paymentMethod->customer !== $user->stripe_customer_id) {
                return $this->errorResponse('Payment method does not belong to the specified user.', 404);
            }

            $paymentMethod->detach();

            return $this->successResponse('Payment method deleted', $paymentMethod);
        } catch (\Exception $e) {
            return $this->errorResponse('Payment method not deleted', 500, ['error' => $e->getMessage()]);
        }
    }
}
