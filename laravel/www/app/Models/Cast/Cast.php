<?php

namespace App\Models\Cast;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name_ja',
        'name_kana',
        'nickname',
        'city',
        'introducer_id',
        'person_in_charge_id',
        'registered_date',
        'status',
        'rank',
        'post_code',
        'prefecture',
        'municipalitie',
        'street',
        'building',
        'station',
        'footwork',
        'alcohol',
        'day_work',
        'night_work',
        'height',
        'three_size_b',
        'three_size_w',
        'three_size_h',
        'cup_size',
        'vip_status',
        'birthday',
        'ceo_check',
        'instagram_id',
        'category_id',
        'tag_of_spec',
        'management_tags',
        'notices',
        'prefecture_and_municipality',
        'smoke',
        'my_comment',
        'siblings',
        'cohabitation',
        'points_holded',
        'point_rate',
        'live_status',
        'work_status',
        'basic_point_price',
        'birthplace',
        'final_academic_background',
        'hair_style',
        'hair_color',
        'score',
        'phone_number',
        'line_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
