<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteProfile extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dob' => ['required', 'date'],
            'gender' => ['required', 'string', 'max:10', 'in:male,female,other'],
            'country' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'neighbor' => ['required', 'string', 'max:255'],
            'lat' => ['required'],
            'lng' => ['required'],
            // 'profile_picture' => ['nullable','mimes:jpeg,bmp,png'],
            'region' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:10'],
        ];
    }
}
