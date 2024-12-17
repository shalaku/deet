<?php

namespace App\Http\Requests;

use App\Exceptions\GlobalException;
use App\Rules\StatusBelongsToCastCategory;
use App\Rules\StatusBelongsToCastLiveCategory;
use App\Rules\StatusBelongsToCastWorkCategory;
use App\Traits\APIResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CastUpdateRequest extends FormRequest
{
    use APIResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_ja' => 'nullable',
            'email' => 'nullable|email|exists:users,email',
            'person_in_charge_id' => 'nullable|exists:users,id',
            'status' => [
                'nullable',
                'integer',
                'exists:statuses,id',
                new StatusBelongsToCastCategory($this->status),
            ],
            'live_status' => [
                'nullable',
                'integer',
                'exists:statuses,id',
                new StatusBelongsToCastLiveCategory($this->live_status),
            ],
            'work_status' => [
                'nullable',
                'integer',
                'exists:statuses,id',
                new StatusBelongsToCastWorkCategory($this->work_status),
            ],
            'rank' => 'nullable',
            'footwork' => 'nullable|in:good,average,bad',
            'alcohol' => 'nullable|in:good,average,bad,not',
            'vip_status' => 'nullable|in:good,average,not',
            'category_id' => 'nullable|exists:cast_categories,id',
            'post_code' => 'nullable|size:7',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new GlobalException('Validation failed', 422, $validator->errors()->toArray());
    }
}
