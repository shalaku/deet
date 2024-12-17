<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestMatchingResource extends JsonResource
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
            'status' => $this->status,
            'municipalities' => $this->municipalities,
            'area_name' => $this->area_name,
            'number_of_people' => $this->number_of_people,
            'requested_start_time' => $this->requested_start_time,
            'requested_matching_term' => $this->requested_matching_term,
            'cast_birthplace' => $this->cast_birthplace,
            'cast_age_min' => $this->cast_age_min,
            'cast_age_max' => $this->cast_age_max,
            'cast_height_min' => $this->cast_height_min,
            'cast_height_max' => $this->cast_height_max,
            'cast_tag' => $this->cast_tag,
            'cast_rank' => $this->cast_rank,
            'mid_night_fee' => $this->mid_night_fee,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'details' => $this->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'matching_id' => $detail->matching_id,
                    'cast_id' => $detail->cast_id,
                    'status' => $detail->status,
                    'designated_point' => $detail->designated_point,
                    'name_ja' => $detail->cast->name_ja ?? "",
                    'nickname' => $detail->cast->nickname ?? "",
                    'cast_status' => $detail->cast->status,
                    'cast_work_status' => $detail->cast->work_status,
                    'cast_live_status' => $detail->cast->live_status,
                    'rank' => $detail->cast->rank,
                    'footwork' => $detail->cast->footwork,
                    'alcohol' => $detail->cast->alcohol,
                    'birthday' => $detail->cast->birthday,
                    'vip_status' => $detail->cast->vip_status,
                ];
            }),
            'customer' => $this->customer ? [
                'id' => $this->customer->id,
                'status_id' => $this->customer->status_id,
                'name_ja' => $this->customer->name_ja,
                'nickname' => $this->customer->nickname ?? "",
            ] : null,    
        ];
    }
}
