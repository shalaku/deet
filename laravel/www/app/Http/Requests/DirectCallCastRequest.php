<?php

namespace App\Http\Requests;

use App\Exceptions\GlobalException;
use App\Rules\StatusBelongsToCastCategory;
use App\Rules\StatusBelongsToCastLiveCategory;
use App\Rules\StatusBelongsToCastWorkCategory;
use App\Traits\APIResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DirectCallCastRequest extends FormRequest
{
    use APIResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'order_type' => 'required|string|in:direct,request',
            'planned_meeting_date_time' => 'required|date_format:Y-m-d H:i:s',
            'planned_meeting_time' => 'required|regex:/^\d{1,2}:[0-5]\d$/',
            // 'place_post_code' => 'required|string',
            'place_prefecture' => 'nullable|string',
            'place_municipalitie' => 'nullable|string',
            'place_street' => 'nullable|string',
            'place_building' => 'nullable|string',
            'place_desc' => 'nullable|string',
            'cast_id' => 'required|exists:casts,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new GlobalException('Validation failed', 422, $validator->errors()->toArray());
    }
}
