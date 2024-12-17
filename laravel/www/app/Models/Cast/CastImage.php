<?php

namespace App\Models\Cast;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastImage extends Model
{
    use HasFactory;
    protected $fillable = ['cast_id', 'image_id'];

    public function images()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

}
