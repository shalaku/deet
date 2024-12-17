<?php

namespace App\Actions;

use App\Http\Resources\CastResource;
use App\Models\Cast\Cast;

class CastLandingPage
{
    public function execute(Cast $cast)
    {
        $profileData = new CastResource($cast);
        // TODO: will be add in_progress, plans, call_request data after complete cast booking


        return ['profile_data' => $profileData];
    }
}
