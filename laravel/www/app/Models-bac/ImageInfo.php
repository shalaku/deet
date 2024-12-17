<?php

namespace App\Models;

use App\Models\Cast\CastImage;
use App\Models\Customer\CustomerImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImageInfo extends Model
{
    use HasFactory;

    protected $table = 'image_infos';

    protected $fillable = [
        'public_url',
        'storage_url',
        'title',
        'alt_text',
        'caption',
        'hash',
        'file_type',
    ];

    public function castImages(): HasMany
    {
        return $this->hasMany(CastImage::class, 'image_id', 'id');
    }

    public function storeImages(): HasMany
    {
        return $this->hasMany(StoreImage::class, 'image_id', 'id');
    }

    public function customerImages(): HasMany
    {
        return $this->hasMany(CustomerImage::class, 'image_id', 'id');
    }

    public function siteAdminImages(): HasMany
    {
        return $this->hasMany(SiteAdminImage::class, 'image_id', 'id');
    }

    public function optimization()
    {
        return $this->hasOne(Optimization::class, 'image_id', 'id');
    }
}
