<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteAdminImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_admin_id',
        'image_id',
    ];
}
