<?php

namespace App\Http\Controllers\API\Payment\V1;

use App\Http\Controllers\Controller;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Stripe\Price;
use Stripe\Stripe;
use Stripe\Product;

class StripeProductController extends Controller
{
    use APIResponse;

    public function getActiveProducts(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $products = Product::all(['active' => true]);

            $formattedProducts = [];

            foreach ($products->data as $product) {
                $prices = Price::all(['product' => $product->id]);

                $formattedProducts[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description ?? '',
                    'active' => $product->active,
                    'prices' => collect($prices->data)->map(function ($price) {
//                        $amount = number_format($price->unit_amount / 100, 2);
//                        $points = (number_format($price->unit_amount / 100, 2) / 1.2);
                        $amount = $price->unit_amount;
                        $points = $amount / 1.2; // Perform calculations on the numeric value

                        return [
                            'price_id' => $price->id,
                            'currency' => strtoupper($price->currency),
                            'unit_amount' => $amount,
                            'points' => $points,
                            'interval' => $price->recurring->interval ?? null,
                            'interval_count' => $price->recurring->interval_count ?? null,
                        ];
                    }),
                ];
            }

            return $this->successResponse('All Stripe Subscription Products.', $formattedProducts);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed', 500, ['error' => $e->getMessage()]);
        }
    }
}
