<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        if ($this->method() == "POST") {
            return [
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users')->whereNull('deleted_at'),
                ],
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'user_roles' => 'nullable|array',
                'password' => 'nullable|string|min:6|confirmed',
            ];
        } else {
            return [
                'id' => 'required|exists:users,id|max:255',
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users')
                        ->ignore($this->id)
                        ->whereNull('deleted_at'),
                ],
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'user_roles' => 'nullable|array',
            ];
        }
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
