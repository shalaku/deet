<?php

namespace App\Models\Customer;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerImage extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'image_id'];

    public function images()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
