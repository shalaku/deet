<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
    ];

    protected $hidden = ['pivot'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(UserList::class, 'location_user', 'location_id', 'user_id');
    }

    public static function findByFilters(array $data, $order_by = 'id', $order = 'DESC', $first = false, $paginate = true, $perPage = 10)
    {
        $query = self::with('users');

        if (isset($data['name'])) {
            $query->where('name', 'like', '%'.$data['name'].'%');
        }

        if (isset($data['latitude']) && isset($data['longitude']) && isset($data['radius'])) {
            $query->selectRaw('
                id, name, latitude, longitude,
                (
                    6371 * ACOS(
                        LEAST(
                            GREATEST(
                                COS(RADIANS(?)) * COS(RADIANS(latitude)) * 
                                COS(RADIANS(longitude) - RADIANS(?)) + 
                                SIN(RADIANS(?)) * SIN(RADIANS(latitude)),
                                -1
                            ),
                            1
                        )
                    )
                ) AS distance
            ', [$data['latitude'], $data['longitude'], $data['latitude']])
                ->having('distance', '<=', $data['radius']);
        } else {
            if (isset($data['latitude'])) {
                $query->where('latitude', $data['latitude']);
            }

            if (isset($data['longitude'])) {
                $query->where('longitude', $data['longitude']);
            }
        }

        if (isset($data['user_id'])) {
            $query->whereHas('users', function ($query) use ($data) {
                $query->where('user_id', $data['user_id']);
            });
        }

        if ($first) {
            $query = $query->orderBy($order_by, $order)->first();
        } elseif ($paginate) {
            $query = $query->orderBy($order_by, $order)->paginate($perPage);
        } else {
            $query = $query->get();
        }

        return $query;
    }
}
