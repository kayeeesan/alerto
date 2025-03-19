<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlertRequest extends FormRequest
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
            'threshold.id' => 'required|exists:thresholds,id',
            'details' => 'required|string',
            'status' => 'required|in:pending,responded,expired',
            'expired_at' => 'nullable|date',
            'response.id' => 'nullable|exists:responses,id',
            'user.id' => 'nullable|exist:user,id'
        ];
    }
}
