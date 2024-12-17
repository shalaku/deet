<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Optimization extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_id',
        'webp_conversion',
        'original_size',
        'optimized_size',
        'webp_size',
        'optimization_status',
    ];

    public function image()
    {
        return $this->belongsTo(ImageInfo::class, 'image_id', 'id');
    }
}
