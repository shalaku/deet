<?php

namespace App\Models\Cast;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastSpecTagList extends Model
{
    use HasFactory;

    protected $fillable = [
        'spec_tag_name',
    ];
}
