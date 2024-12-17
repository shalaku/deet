<?php

namespace App\Models\Order;

use App\Models\Customer\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestMatching extends Model
{
    use HasFactory;

    protected $fillable = [
        'shown_id',
        'customer_id',
        'status',
        'municipalities',
        'area_name',
        'number_of_people',
        'requested_start_time',
        'requested_matching_term',
        'cast_birthplace',
        'cast_age_min',
        'cast_age_max',
        'cast_height_min',
        'cast_height_max',
        'cast_tag',
        'cast_rank',
        'mid_night_fee',
    ];

    protected $casts = [
        'cast_tag' => 'array',
        'cast_rank' => 'array',
        'requested_start_time' => 'datetime',
    ];

    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function details()
    {
        return $this->hasMany(RequestMatchingDetail::class, 'matching_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

}
