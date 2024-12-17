<?php

namespace App\Actions;

use App\Http\Resources\CastResource;
use App\Models\Cast\Cast;

class CastAction
{
    public function execute(array $data)
    {
        $query = Cast::query();
        if ($data['type'] == 'age') {
            $minAge = explode('-', $data['value'])[0];
            $maxAge = explode('-', $data['value'])[1];

            if (!is_null($minAge)) {
                $query->whereRaw('FLOOR(DATEDIFF(CURDATE(), birthday) / 365) >= ?', [$minAge]);
            }

            if (!is_null($maxAge)) {
                $query->whereRaw('FLOOR(DATEDIFF(CURDATE(), birthday) / 365) <= ?', [$maxAge]);
            }
        } elseif ($data['type'] == 'new_cast') {
            $query->where('status', 100);
            $query->orderBy('registered_date', 'desc');
        } elseif ($data['type'] == 'popular_cast') {
            $query->where('rank', 'A');
        } elseif ($data['type'] == 'available_cast') {
            $query->where('work_status', 110)->where('live_status', 120);
        }

        $casts = $query->paginate($data['limit'], ['*'], 'page', $data['page']);

        return [
            $data['type'] => CastResource::collection($casts),
            'total' => $casts->total(),
            'pages' => $casts->lastPage(),
        ];
    }
}
