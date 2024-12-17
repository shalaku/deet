<?php

namespace App\Http\Requests;

use App\Exceptions\GlobalException;
use App\Traits\APIResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
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
            'points' => 'required|numeric|exists:payment_amount_points,points',
            'user_id' => 'required|integer|exists:users,id',
            'user_type' => 'required|string',
            'currency' => 'required|string',
            'description' => 'nullable|string',
            'email' => 'required|email',
            'payment_for' => 'nullable|string'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new GlobalException('Validation failed', 422, $validator->errors()->toArray());
    }
}
