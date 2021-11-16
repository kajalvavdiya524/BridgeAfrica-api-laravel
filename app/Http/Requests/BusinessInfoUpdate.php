<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessInfoUpdate extends FormRequest
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
            'name' => ['required', 'string'],
            'category' => ['required', 'string'],
            'timezone' => ['required'],
            'keywords' => ['required', 'string'],
            'language' => ['required', 'string'],
            'about_business' => ['required', 'string'],
            'lat' =>  ['required'],
            'lng' =>  ['required'],
            'region' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:10'],
            'phone' => ['numeric'],
            'email' => ['email']
        ];
    }
}
