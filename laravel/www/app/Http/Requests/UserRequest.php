<?php

namespace App\Http\Requests;

use App\Exceptions\UserException;
use App\Traits\APIResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
            'name_ja'      => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email',
            'password'     => 'required|string|min:8',
            'account_type' => 'required|string|in:admin,store,cast,customer',
            'user_type'    => 'required|string|in:admin,staff',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new UserException('Validation failed', 422, $validator->errors()->toArray());
    }
}
