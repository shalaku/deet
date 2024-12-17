<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageSizes extends Model
{
    use HasFactory;

    protected $table = 'image_sizes';

    protected $fillable = [
        'variation_name',
        'height',
        'width',
        'aspect_ratio',
    ];
}
