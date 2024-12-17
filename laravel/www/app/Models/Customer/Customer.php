<?php

namespace App\Models\Customer;

use App\Models\Order\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status_id',
        'name_ja',
        'name_kana',
        'nickname',
        'birthday',
        'introducer_id',
        'person_in_charge_id',
        'category_id',
        'registered_date',
        'tag_of_preference',
        'notices',
        'height',
        'municipalitie',
        'street',
        'building',
        'type',
        'work',
        'hair',
        'hair_style',
        'alcohol',
        'education',
        'tobacco',
        'siblings',
        'cohabitant',
        'fav_cast_ids',
        'points_holded',
        'post_code',
        'prefecture',
        'phone_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
