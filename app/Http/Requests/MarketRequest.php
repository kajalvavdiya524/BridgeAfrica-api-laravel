<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketRequest extends FormRequest
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
            'name' =>  'required|string',
            'description' =>  'required|string',
            'picture' =>  'required|mimes:jpeg,png,jpg|max:2048',
            'price' =>  'required|numeric',
            'discount_price' =>  'numeric|nullable',
            'on_discount' => 'required|boolean',
            'condition' =>  'required|string',
            'business_id' =>  'required|integer|exists:businesses,id',
            'categoryId.*' => 'required|integer|exists:categories,id',
            'subCategoryId.*' => 'required|integer|exists:subcategories,id',
            'filterId.*' => 'required|integer|exists:filters,id'
        ];
    }
}
