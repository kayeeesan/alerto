<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffRequest extends FormRequest
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
           'username' => 'required|string|unique:staffs,username|unique:users,username',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'mobile_number' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'government_agency' => 'required|string',
            'region_id' => 'required|exists:regions,id',
            'province_id' => 'required|exists:provinces,id',
            'municipality_id' => 'required|exists:municipalities,id',
            'river_id' => 'required|exists:rivers,id',
            'lgu_fb' => 'nullable|string',
            'status' => 'required|in:pending,approved,disabled',
        ];
    }
}
