<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThresholdRequest extends FormRequest
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
            'sensor.id' => 'required|exists:sensors_under_alertos,id',
            'baseline' => 'required|numeric|min:0',
            'sixty_percent' => 'required|numeric|min:0',
            'eighty_percent' => 'required|numeric|min:0',
            'one_hundred_percent' => 'required|numeric|min:0',
            'xs_date' => 'nullable|date',
            'water_level' => 'nullable|numeric|min:0'
        ];
    }
}
