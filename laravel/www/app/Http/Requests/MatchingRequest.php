<?php

namespace App\Http\Requests;

use App\Exceptions\GlobalException;
use App\Traits\APIResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MatchingRequest extends FormRequest
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
            'status' => 'integer|exists:statuses,id',
            'municipalities' => 'nullable|string|max:64',
            'area_name' => 'nullable|string|max:64',
            'number_of_people' => 'nullable|integer|min:1',
            'requested_start_time' => 'required|date_format:Y-m-d H:i:s',
            'requested_matching_term' => 'required|regex:/^\d{1,2}:[0-5]\d$/',
            'cast_birthplace' => 'nullable|string|max:32',
            'cast_age_min' => 'nullable|integer|min:0',
            'cast_age_max' => 'nullable|integer|min:0',
            'cast_height_min' => 'nullable|integer|min:0',
            'cast_height_max' => 'nullable|integer|min:0',
            'cast_tag' => 'nullable|array',
            'cast_rank' => 'nullable|array',
            'mid_night_fee' => 'nullable|integer|min:0',
            'selected_cast_ids' => 'nullable|string'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new GlobalException('Validation failed', 422, $validator->errors()->toArray());
    }
}
