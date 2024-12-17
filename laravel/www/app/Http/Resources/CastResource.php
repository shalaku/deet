<?php

namespace App\Http\Resources;

use App\Models\Cast\CastBankAccDetails;
use App\Models\Cast\CastImage;
use App\Models\Image;
use Illuminate\Http\Resources\Json\JsonResource;

class CastResource extends JsonResource
{
    public function toArray($request)
    {

        $bankDetails = CastBankAccDetails::select('bank_name', 'account_number', 'branch', 'account_type')->where('cast_id', $this->id)->first();
        $castImages = CastImage::where('cast_id', $this->id)->pluck('image_id')->toArray();

        $responseImage = [];
        if (!empty($castImages)) {
            $images = Image::whereIn('id', $castImages)->get();

            foreach ($images as $item) {
                $responseImage[] = [
                    'id' => $item['id'],
                    'title' => $item['title'] ?? null,
                    'image_url' => config('filesystems.disks.s3.url') . '/' . $item['image_path'],
                    'file_type' => $item['file_type'] ?? null,
                    'is_profile_picture'=> $item['is_profile_picture'] ?? false,
                ];
            }
        }

        $response = [];

        $response['cast'] =  [
            'id' => $this->id,
            'name_ja' => $this->name_ja,
            'name_kana' => $this->name_kana,
            'nickname' => $this->nickname,
            'city' => $this->city,
            'introducer_id' => $this->introducer_id,
            'person_in_charge_id' => $this->person_in_charge_id,
            'registered_date' => $this->registered_date,
            'status' => $this->status,
            'rank' => $this->rank,
            'post_code' => $this->post_code,
            'prefecture' => $this->prefecture,
            'municipalitie' => $this->municipalitie,
            'street' => $this->street,
            'building' => $this->building,
            'station' => $this->station,
            'footwork' => $this->footwork,
            'alcohol' => $this->alcohol,
            'day_work' => $this->day_work,
            'night_work' => $this->night_work,
            'height' => $this->height,
            'three_size_b' => $this->three_size_b,
            'three_size_w' => $this->three_size_w,
            'three_size_h' => $this->three_size_h,
            'cup_size' => $this->cup_size,
            'vip_status' => $this->vip_status,
            'birthday' => $this->birthday,
            'ceo_check' => $this->ceo_check,
            'instagram_id' => $this->instagram_id,
            'category_id' => $this->category_id,
            'tag_of_spec' => !empty($this->tag_of_spec) ? json_decode($this->tag_of_spec, true) : null,
            'management_tags' => !empty($this->management_tags) ? json_decode($this->management_tags, true) : null,
            'notices' => $this->notices,
            'prefecture_and_municipality' => $this->prefecture_and_municipality,
            'smoke' => $this->smoke,
            'my_comment' => $this->my_comment,
            'siblings' => $this->siblings,
            'cohabitation' => $this->cohabitation,
            'points_holded' => $this->points_holded,
            'point_rate' => $this->point_rate,
            'live_status' => $this->live_status,
            'work_status' => $this->work_status,
            'basic_point_price' => $this->basic_point_price,
            'birthplace' => $this->birthplace,
            'final_academic_background' => $this->final_academic_background,
            'hair_style' => $this->hair_style,
            'hair_color' => $this->hair_color,
            'score' => $this->score,
            'phone_number' => $this->phone_number,
            'line_id' => $this->line_id,
            'users_table_id' => $this->user->id,
            'email' => $this->user->email,
            'user_id' => $this->user->user_id,
            'user_sig' => $this->user->user_sign,
            'sdkAppId' => config('tencent.chat.sdkAppId')
        ];

        $response['bank_details'] = empty($bankDetails) ? [] : $bankDetails;
        $response['images'] = $responseImage;
        $response['compatible_areas'] = [
            "Prefecture",
            "Ichimachi",
            "Ichimachi1",
            "Ichimachi12"
        ];

        return $response;
    }
}
