<?php

namespace App\Models\Order;

use App\Models\Cast\Cast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchingOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'cast_id', 'order_acception_status', 'start_date_time', 'end_date_time', 'applied_point',
        'mid_night_fee', 'extra_charge', 'designated_point', 'total_point', 'cast_memo'
    ];

    public function order()
    {
        return $this->belongsTo(MatchingOrder::class, 'order_id');
    }

    public function cast()
    {
        return $this->belongsTo(Cast::class, 'cast_id');
    }
}
