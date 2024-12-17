<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PointHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'order_id' => $this->order_id,
            'point_amount' => $this->point_amount,
            'pay_amount' => $this->pay_amount,
            'payment_status' => $this->payment_status,
            'sell_date_time' => $this->sell_date_time,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'cast' => new CastResource($this->whenLoaded('cast')),
            'matching_order' => new MatchingOrderResource($this->whenLoaded('matching_order')),
            'created_at' => $this->created_at,            
            'updated_at' => $this->updated_at,
        ];
    }
}