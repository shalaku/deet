<?php

namespace App\Http\Requests;

use App\Exceptions\CustomerException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerUpdateRegisterRequest extends FormRequest
{
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
            'status_id' => 'nullable|exists:statuses,id',
            'name_ja' => 'nullable|string',
            'name_kana ' => 'nullable|string',
            'nickname ' => 'nullable|string',
            'birthday' => 'nullable|date',
            'person_in_charge_id' => 'nullable|exists:users,id',
            'introducer_id' => 'nullable|exists:casts,id',
            'category_id' => 'nullable|exists:customer_category_lists,id',
            'registered_date' => 'nullable|date',
            'tag_of_preference' => 'nullable|json',
            'fav_cast_ids' => 'nullable|json',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new CustomerException('Validation failed', 422, $validator->errors()->toArray());

    }
}
