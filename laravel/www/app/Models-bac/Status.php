<?php

namespace App\ModelsBac;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status_lists';

    protected $fillable = [
        'status_category',
        'status_name',
        'desc',
    ];
}
