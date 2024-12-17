<?php

namespace App\Models\Order;

use App\Models\Customer\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'shown_id','customer_id', 'order_type', 'status', 'planned_meeting_date_time',
        'planned_meeting_time', 'place_post_code', 'place_prefecture',
        'place_municipalitie', 'place_street', 'place_building', 'place_desc',
        'start_date_time', 'end_date_time', 'number_of_people', 'admin_memo', 'cast_memo',
    ];

    public function details()
    {
        return $this->hasMany(MatchingOrderDetail::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    
}
