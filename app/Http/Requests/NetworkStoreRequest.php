<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NetworkStoreRequest extends FormRequest
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
            'business_id' => 'required|integer|exists:businesses,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'purpose' => 'required|string',
            'special_needs' => 'required|string',
            'address' => 'required|string|max:255',
            'country_id' => 'required|integer|exists:countries,id',
            'region_id' => 'required|integer|exists:regions,id',
            'division_id' => 'required|integer|exists:divisions,id',
            'council_id' => 'required|integer|exists:councils,id',
            'neighborhood_id.*' => 'exists:neighborhoods,id|nullable',
            'image' => 'required|mimes:jpeg,png,jpg|max:2048',
            'email' => 'required|email|max:100',
            'primary_phone' => 'required|numeric',
            'secondary_phone' => 'numeric|nullable',
            'network_categories' => 'string|nullable|max:255'
        ];
    }
}
