<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiverRequest extends FormRequest
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
                'municipality.id' => 'required|exists:municipalities,id',
                'name' => 'required|string|max:255'
            ];
           } else {
            return [
                  'name' => 'required|string|max:255',
                    'id' => 'required|exists:rivers,id|max:255'
            ];
           }
    }
}
