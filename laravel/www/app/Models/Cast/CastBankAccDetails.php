<?php

namespace App\Models\Cast;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastBankAccDetails extends Model
{
    use HasFactory;

    protected $fillable = ['cast_id', 'bank_name', 'account_number', 'branch', 'account_type'];
}
