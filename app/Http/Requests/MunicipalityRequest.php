<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MunicipalityRequest extends FormRequest
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
            'province.id' => 'required|exists:provinces,id',
             'name' => 'required|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Municipality Type name is required.',
            'id.required'  => 'Municipality Type does not exist'
        ];
    }
}
