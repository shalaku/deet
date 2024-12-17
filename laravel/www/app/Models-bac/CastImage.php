<?php

namespace App\ModelsBac;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'cast_id',
        'image_id',
    ];
}
