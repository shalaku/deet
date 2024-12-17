<?php

namespace App\Actions;
use App\Exceptions\GlobalException;
use App\Models\Cast\Cast;
use App\Models\Order\MatchingOrder;
use App\Models\Order\MatchingOrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\OrderHelper;

class MatchingOrderProcessAction
{
    public function execute($requestMatchingData, $requestMatchingDetails, $orderType = 'request')
    {

        $rank_point_arr = array(
            'BLACK'=>10000,
            'PLATINUM'=>8000,
            'GOLD'=>6000,        
        );
        
        DB::beginTransaction();
        try {

            $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $requestMatchingData['requested_start_time']);
            list($hours, $minutes) = explode(':', $requestMatchingData['requested_start_time']);

            $endDateTime = (clone $startDateTime)->addHours($hours)->addMinutes($minutes);

            $isLateNight = (
                ($startDateTime->hour >= 0 && ($startDateTime->hour < 5 || ($startDateTime->hour == 5 && $startDateTime->minute == 0))) ||
                ($endDateTime->hour >= 0 && ($endDateTime->hour < 5 || ($endDateTime->hour == 5 && $endDateTime->minute == 0))) ||
                ($startDateTime->hour < 5 && $endDateTime->hour > 5) ||
                ($startDateTime->hour > 5 && $endDateTime->hour > 5 && $startDateTime->hour > $endDateTime->hour)
            );

            $matchingOrderData = [
                "customer_id" => $requestMatchingData['customer_id'],
                "order_type" => $orderType,
                "status" => 601,
                "planned_meeting_date_time" => $requestMatchingData['requested_start_time'],
                "planned_meeting_time" => $requestMatchingData['requested_matching_term'],
                "place_post_code" => null,
                "place_prefecture" => null,
                "place_municipalitie" => null,
                "place_street" => $requestMatchingData['area_name'],
                "place_building"  => null,
                "place_desc" => $requestMatchingData['area_name'],
                "start_date_time"  => null,
                "end_date_time" => null,
                "number_of_people" => $requestMatchingData['number_of_people'],
            ];

            $matchingOrder = MatchingOrder::create($matchingOrderData);

            $matchingOrderDetails = [];

            foreach ($requestMatchingDetails as $requestMatchingDetail) {
                $castData = Cast::where('id', $requestMatchingDetail['cast_id'])->first();
                if($orderType == 'request'){
                    $applied_point = $rank_point_arr[$castData['rank']];
                } else {
                    $applied_point = $castData['basic_point_price'];
                }

                $calculated = OrderHelper::calculateOrderPoints(
                    $requestMatchingData['requested_matching_term'],
                    $requestMatchingData['requested_start_time'],
                    false,
                    $applied_point,
                    $requestMatchingDetail['designated_point'], 
                );

                $matchingOrderDetails[] = [
                    'order_id' => $matchingOrder->id,
                    'cast_id' => $requestMatchingDetail['cast_id'],
                    'order_acception_status' =>701,
                    'start_date_time' => null,
                    'end_date_time' => null,
                    'mid_night_fee' => $calculated['mid_night_fee'],
                    'extra_charge' => $calculated['extra_charge'],
                    'designated_point' => $calculated['designated_point'],
                    'total_point' => $calculated['total_point'],
                    'applied_point' => $applied_point,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            MatchingOrderDetail::insert($matchingOrderDetails);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            Log::error('Matching Order Create Failed. Request Matching Id: ' .$requestMatchingData->id . '. Error Message: '. $th->getMessage());
            throw new GlobalException('Matching Order Create Failed.', 422, (array)$th->getMessage());        }
    }
}
