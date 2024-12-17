<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    protected $fillable = [
        'subscription_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'plan_id',
        'status',
        'current_period_start',
        'current_period_end',
    ];
}
