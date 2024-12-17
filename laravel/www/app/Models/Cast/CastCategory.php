<?php

namespace App\Models\Cast;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_name'];
}
