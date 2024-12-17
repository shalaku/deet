<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAmountPoint extends Model
{
    use HasFactory;

    protected $table = 'payment_amount_points';

    protected $fillable = ['points'];
}
