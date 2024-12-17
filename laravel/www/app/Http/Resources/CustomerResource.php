<?php

namespace App\Http\Resources;


use App\Models\Customer\CustomerImage;
use App\Models\Image;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request)
    {
        $customerImages = CustomerImage::where('customer_id', $this->id)->pluck('image_id')->toArray();

        $responseImage = [];
        if (!empty($customerImages)) {

            $latestImage = Image::whereIn('id', $customerImages)->orderBy('created_at', 'desc')->first();
            if (!empty($latestImage)) {
                $responseImage[] = [
                    'id' => $latestImage['id'],
                    'title' => $latestImage['title'] ?? null,
                    'image_url' => config('filesystems.disks.s3.url') . '/' . $latestImage['image_path'],
                    'file_type' => $latestImage['file_type'] ?? null,
                    'is_profile_picture'=> 1,
                ];
            }
        }
//        dd(!empty($this->tag_of_preference) ? json_decode($this->tag_of_preference, true) : null);
        return [
            'id' => $this->id,
            'status_id' => $this->status_id,
            'name_ja' => $this->name_ja,
            'name_kana' => $this->name_kana,
            'nickname' => $this->nickname,
            'birthday' => $this->birthday,
            'introducer_id' => $this->introducer_id,
            'person_in_charge_id' => $this->person_in_charge_id,
            'category_id' => $this->category_id,
            'post_code' => $this->post_code,
            'prefecture' => $this->prefecture,
            'registered_date' => $this->registered_date,
            'tag_of_preference' => !empty($this->tag_of_preference) ? json_decode($this->tag_of_preference, true) : null,
            'notices' => $this->notices,
            'height' => $this->height,
            'municipalitie' => $this->municipalitie,
            'street' => $this->street,
            'building' => $this->building,
            'phone_number' => $this->phone_number,
            'type' => $this->type,
            'work' => $this->work,
            'hair' => $this->hair,
            'hair_style' => $this->hair_style,
            'alcohol' => $this->alcohol,
            'education' => $this->education,
            'tobacco' => $this->tobacco,
            'siblings' => $this->siblings,
            'cohabitant' => $this->cohabitant,
            'fav_cast_ids' => !empty($this->fav_cast_ids) ? json_decode($this->fav_cast_ids, true) : null,
            'points_holded' => $this->points_holded,
            'images' => $responseImage,
            'email' => $this->user->email,
            'users_table_id' => $this->user->id,
            'user_id' => $this->user->user_id,
            'user_sig' => $this->user->user_sign,
            'sdkAppId' => config('tencent.chat.sdkAppId')
        ];
    }
}
