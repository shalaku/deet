<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCastRequest extends FormRequest
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
            'name_ja' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'person_in_charge_id' => 'required|exists:user_lists,id',
            'status_id' => 'required|exists:status_lists,id',
            'rank' => 'required',
            'footwork' => 'required|in:good,average,bad',
            'alcohol' => 'required|in:good,average,bad,not',
            'vip_status' => 'nullable|in:good,average,not',
            'category_id' => 'required|exists:cast_category_lists,id',
            'post_code' => 'nullable|size:7',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = responseJson(false, 'Validation error occurred', $validator->errors()->toArray(), 422);

        throw new HttpResponseException($response);
    }
}
