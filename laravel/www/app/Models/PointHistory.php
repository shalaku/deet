<?php

namespace App\Models;

use App\Models\Cast\Cast;
use App\Models\Customer\Customer;
use App\Models\Order\MatchingOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'point_amount',
        'pay_amount',
        'payment_status',
        'sell_date_time',
        'created_at',
        'updated_at',
        'trx_id'
    ];

    public function cast()
    {
        return $this->belongsTo(Cast::class, 'user_id', 'user_id'); // user_idを使用
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id','user_id'); // user_idを使用
    }
    
    public function matching_order()
    {
        return $this->belongsTo(MatchingOrder::class, 'order_id', 'id')->with(['details', 'customer']);
    }
    
}
