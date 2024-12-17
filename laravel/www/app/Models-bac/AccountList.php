<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ja',
        'chef_admin',
        'post_code',
        'prefectures',
        'municipalities',
        'address_1',
        'address_2',
        'phone',
        'email',
    ];

    public function users()
    {
        return $this->hasMany(UserList::class);
    }
}
