<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchingOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shown_id' => $this->shown_id,
            'customer_id' => $this->customer_id,
            'order_type' => $this->order_type,
            'status' => $this->status,
            'planned_meeting_date_time' => $this->planned_meeting_date_time,
            'planned_meeting_time' => $this->planned_meeting_time,
            'place_post_code' => $this->place_post_code,
            'place_prefecture' => $this->place_prefecture,
            'place_municipalitie' => $this->place_municipalitie,
            'place_street' => $this->place_street,
            'place_building' => $this->place_building,
            'place_desc' => $this->place_desc,
            'start_date_time' => $this->start_date_time,
            'end_date_time' => $this->end_date_time,
            'number_of_people' => $this->number_of_people,
            'admin_memo' => $this->admin_memo,
            'cast_memo' => $this->cast_memo,
            'created_at' => $this->created_at,
            'details' => $this->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'order_id' => $detail->order_id,
                    'cast_id' => $detail->cast_id,
                    'name_ja' => $detail->cast->name_ja ?? "",
                    'nickname' => $detail->cast->nickname ?? "",
                    'birthday' => $detail->cast->birthday,
                    'work_status' => $detail->cast->work_status,
                    'order_acception_status' => $detail->order_acception_status,
                    'start_date_time' => $detail->start_date_time,
                    'end_date_time' => $detail->end_date_time,
                    'applied_point' => $detail->applied_point,
                    'mid_night_fee' => $detail->mid_night_fee,
                    'extra_charge' => $detail->extra_charge,
                    'designated_point' => $detail->designated_point,
                    'total_point' => $detail->total_point,
                    'cast_status' => $detail->cast->status,
                    'cast_work_status' => $detail->cast->work_status,
                    'cast_live_status' => $detail->cast->live_status,
                    'rank' => $detail->cast->rank,
                    'footwork' => $detail->cast->footwork,
                    'alcohol' => $detail->cast->alcohol,
                    'vip_status' => $detail->cast->vip_status,
                    'point_rate' => $detail->cast->point_rate,
                    'cast_memo' => $detail->cast_memo,
                ];
            }),
            'customer' => $this->customer ? [
                'id' => $this->customer->id,
                'status_id' => $this->customer->status_id,
                'name_ja' => $this->customer->name_ja,
                'nickname' => $this->customer->cast->nickname ?? "",
            ] : null,           
        ];
    }
}
