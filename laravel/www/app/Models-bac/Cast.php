<?php

namespace App\Models;

use App\Models\Cast\CastImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cast extends Model
{
    use HasFactory;

    protected $table = 'cast_lists';

    protected $fillable = [
        'name_ja',
        'name_kana',
        'city',
        'introducer_id',
        'person_in_charge_id',
        'registered_date',
        'status_id',
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
        'vip_status',
        'birthday',
        'ceo_check',
        'instagram_id',
        'category_id',
        'tag_of_spec',
        'notices',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function introducer()
    {
        return $this->belongsTo(self::class, 'introducer_id');
    }

    public function personInCharge()
    {
        return $this->belongsTo(UserList::class, 'person_in_charge_id');
    }

    public function category()
    {
        return $this->belongsTo(CastCategoryList::class);
    }

    public function castImages(): HasMany
    {
        return $this->hasMany(CastImage::class);
    }

    public function createNew(array $data): self
    {
        $obj = new self;
        $obj->user_id = $data['user_id'];
        $obj->name_ja = $data['name_ja'];
        $obj->name_kana = $data['name_kana'] ?? null;
        $obj->city = $data['city'] ?? null;
        $obj->introducer_id = $data['introducer_id'] ?? null;
        $obj->person_in_charge_id = $data['person_in_charge_id'];
        $obj->registered_date = $data['registered_date'] ?? null;
        $obj->status_id = $data['status_id'];
        $obj->rank = $data['rank'];
        $obj->post_code = $data['post_code'] ?? null;
        $obj->prefecture = $data['prefecture'] ?? null;
        $obj->municipalitie = $data['municipalitie'] ?? null;
        $obj->street = $data['street'] ?? null;
        $obj->building = $data['building'] ?? null;
        $obj->station = $data['station'] ?? null;
        $obj->footwork = $data['footwork'];
        $obj->alcohol = $data['alcohol'];
        $obj->day_work = $data['day_work'] ?? null;
        $obj->night_work = $data['night_work'] ?? null;
        $obj->height = $data['height'] ?? null;
        $obj->three_size_b = $data['three_size_b'] ?? null;
        $obj->three_size_w = $data['three_size_w'] ?? null;
        $obj->three_size_h = $data['three_size_h'] ?? null;
        $obj->vip_status = $data['vip_status'] ?? null;
        $obj->birthday = $data['birthday'] ?? null;
        $obj->ceo_check = $data['ceo_check'] ?? null;
        $obj->instagram_id = $data['instagram_id'] ?? null;
        $obj->category_id = $data['category_id'] ?? null;
        $obj->tag_of_spec = $data['tag_of_spec'] ?? null;
        $obj->notices = $data['notices'] ?? null;
        $obj->save();

        return $obj;
    }
}
