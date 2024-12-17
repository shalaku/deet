<?php
namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderHelper
{

    public static function calculateOrderPoints(
        $plannedMeetingTime,
        $startDateTime,
        $endDateTime = false,
        $basePoint,
        $designated = false
    ) {
        $startDateTime = Carbon::parse($startDateTime);
    
        // $endDateTimeがfalseの場合、$startDateTimeに$plannedMeetingTimeを加算
        if ($endDateTime === false) {
            list($hours, $minutes) = explode(':', $plannedMeetingTime);
            $endDateTime = $startDateTime->copy()->addHours($hours)->addMinutes($minutes);
        } else {
            $endDateTime = Carbon::parse($endDateTime);
        }
    
        // 深夜料金の計算
        $isLateNight = (
            ($startDateTime->hour >= 0 && ($startDateTime->hour < 5 || ($startDateTime->hour == 5 && $startDateTime->minute == 0))) ||
            ($endDateTime->hour >= 0 && ($endDateTime->hour < 5 || ($endDateTime->hour == 5 && $endDateTime->minute == 0))) ||
            ($startDateTime->hour < 5 && $endDateTime->hour > 5) ||
            ($startDateTime->hour > 5 && $endDateTime->hour > 5 && $startDateTime->hour > $endDateTime->hour)
        );
    
        $mid_night_fee = $isLateNight ? 5000 : 0;
    
        // 時間の差を計算
        $durationInMinutes = $endDateTime->diffInMinutes($startDateTime);
    
        // 予定時間を分で算出
        list($hours, $minutes) = explode(':', $plannedMeetingTime);
        $plannedMinutes = ($hours * 60) + $minutes;
    
        $extra_charge = 0;
        if ($plannedMinutes < $durationInMinutes) {
            $extra_charge = ceil(($durationInMinutes - $plannedMinutes) / 30) * $basePoint * 1.3;
        }
    
        if ($plannedMinutes > $durationInMinutes) {
            $durationInMinutes = $plannedMinutes;
        }
    
        // 30分単位で消費ポイントを計算
        $pointsConsumed = ceil($durationInMinutes / 30) * $basePoint;
    
        $designated_fee = (int)$designated ? ceil($durationInMinutes / 30) * 1500 : 0;
    
        return array(
            'total_point' => $pointsConsumed + $mid_night_fee + $extra_charge + $designated_fee,
            'mid_night_fee' => $mid_night_fee,
            'extra_charge' => $extra_charge,
            'designated_point' => $designated_fee,
        );
    }

    public static function generateShownId($orderType = 'order', $length = 7) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        if($orderType == 'order') {
            $prefix = "ORD";
        } else if($orderType == 'request') {
            $prefix = "REQ";
        }

        do {
            $randomBytes = random_bytes($length);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[ord($randomBytes[$i]) % $charactersLength];
            }
            $randomString = $prefix.$randomString;
        } while (self::shownIdExists($randomString));

        return $randomString;
    }

    private static function shownIdExists($shownId) {
        // データベース内で`shown_id`が存在するかを確認するクエリを実行
        return DB::table('matching_orders')->where('shown_id', $shownId)->exists() ||
               DB::table('request_matchings')->where('shown_id', $shownId)->exists();
    }
}