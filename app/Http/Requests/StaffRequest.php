<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'username' => 'required|email|unique:users,username',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'role.id' => 'required|exists:roles,id',
            'region.id' => 'required|exists:regions,id',
            'province.id' => 'required|exists:provinces,id',
            'municipality.id' => 'required|exists:municipalities,id',
            'river.id' => 'required|exists:rivers,id',
            'fb_lgu' => 'required|string|max:255',
            // password is nullable here
            'password' => 'nullable|string|min:6|confirmed',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $staffId = $this->route('staff'); // Update, password is nullable
            if ($staffId) {
                $staff = \App\Models\Staff::find($staffId);
                if ($staff && $staff->user) {
                    $rules['username'] = 'required|email|unique:users,username,' . $staff->user->id;
                }
            }
        }

        return $rules;
    }

}