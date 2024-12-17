<?php

namespace App\Models\Order;

use App\Models\Cast\Cast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestMatchingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'matching_id','cast_id','status','designated_point'
    ];

    public function request()
    {
        return $this->belongsTo(RequestMatching::class, 'matching_id');
    }

    public function cast()
    {
        return $this->belongsTo(Cast::class, 'cast_id');
    }
}
