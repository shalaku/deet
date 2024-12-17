<?php

namespace App\Http\Controllers\GPS;

use App\Http\Controllers\Controller;
use App\Http\Requests\GpsStoreRequest;
use App\Models\Location;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $paginate = $request->paginate ?? false;
        $per_page = $request->per_page ?? 10;
        $order_by = $request->order_by ?? 'id';
        $order = $request->order ?? 'DESC';

        $locations = Location::findByFilters($request->all(), $order_by, $order, false, $paginate, $per_page);

        $message = $locations->count() > 0 ? 'Locations fetched successfully' : 'No locations found';

        return responseJson(true, $message, $locations);
    }

    public function store(GpsStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $location_obj = Location::create([
                'name' => $request->name,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            $location_obj->users()->attach([auth()->user()->id]);

            DB::commit();

            return responseJson(true, 'Location created successfully');
        } catch (Exception $e) {
            DB::rollback();

            Log::info($e->getMessage().' '.$e->getLine());

            return responseJson(false, 'Something went wrong', [], $e->getCode());
        }
    }
}
