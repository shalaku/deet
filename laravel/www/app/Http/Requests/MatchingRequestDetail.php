<?php

namespace App\Http\Requests;

use App\Exceptions\GlobalException;
use App\Traits\APIResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MatchingRequestDetail extends FormRequest
{
    use APIResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'matching_id' => 'required|exists:request_matchings,id',
            'cast_id' => 'required|exists:casts,id',
            'status' => 'required|exists:statuses,id',       
            'designated' => 'nullable',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new GlobalException('Validation failed', 422, $validator->errors()->toArray());
    }
}
