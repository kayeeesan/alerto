<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SensorUnderAlertoRequest extends FormRequest
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
            'device_id' => 'required|numeric|min:0',
            'device_water_level' => 'nullable|numeric|min:0',
            'device_rain_amount' => 'nullable|numeric|min:0',
            'river.id' => 'required|exists:rivers,id',
            'municipality.id' => 'required|exists:municipalities,id',
            'long' => 'required|numeric|min:0',
            'lat' => 'required|numeric|min:0',
            'sensor_type' => 'required|string'
        ];
    }
}
