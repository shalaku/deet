<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastRankBasePoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'rank_name',
        'point_amount',
    ];
}
