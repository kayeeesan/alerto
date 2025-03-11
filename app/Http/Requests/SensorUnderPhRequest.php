<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SensorUnderPhRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'baseline' => 'required|numeric|min:0',
            'sixty_percent' => 'required|numeric|min:0',
            'eighty_percent' => 'required|numeric|min:0',
            'one_hundred_percent' => 'required|numeric|min:0'
        ];
    }
}
