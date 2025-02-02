<?php

namespace App\Http\Requests;

use App\Exceptions\CustomerException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerRegisterRequest extends FormRequest
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
            'status_id' => 'required|exists:statuses,id',
            'name_ja' => 'required',
            'birthday' => 'nullable|date',
            'person_in_charge_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:customer_category_lists,id',
            'introducer_id' => 'nullable|exists:casts,id',
            'registered_date' => 'required|date',
            'tag_of_preference' => 'nullable|json',
            'email' => 'required',
            'phone_number' => 'string',
            'nickname' => 'required',
            'password' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new CustomerException('Validation failed', 422, $validator->errors()->toArray());

    }
}
