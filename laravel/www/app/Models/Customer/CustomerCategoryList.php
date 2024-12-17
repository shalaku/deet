<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCategoryList extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
    ];
}
