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
            'river.id' => 'required|exists:rivers,id',
            'sensor.id' => 'required|exists:sensors_under_alertos,id',
            'municipality.id' => 'required|exists:municipalities,id',
            'xs_date' => 'nullable|date',
        ];
    }
}
