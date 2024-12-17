<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'plan_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'plan_id',
        'status',
        'current_period_start',
        'current_period_end'
    ];

    public function histories(): HasMany
    {
        return $this->hasMany(SubscriptionHistory::class);
    }
}
