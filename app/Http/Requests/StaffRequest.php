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
            // 'user_id' => 'required|exists:users,id',
            'username' => 'required|string|max:255|unique:staffs,username',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15',
            'role.id' => 'required|exists:roles,id',
            'region.id' => 'required|exists:regions,id',
            'province.id' => 'required|exists:provinces,id',
            'municipality.id' => 'required|exists:municipalities,id',

            'river.id' => 'required|exists:rivers,id',
        ];
    }
}
