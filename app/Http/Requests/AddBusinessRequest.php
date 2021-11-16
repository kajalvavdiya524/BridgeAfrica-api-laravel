<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBusinessRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'country' => explode(',', $this->country),
            'region' => explode(',', $this->region),
            'council' => explode(',', $this->council),
            'division' => explode(',', $this->division),
            'neigborhood' => explode(',', $this->neigborhood),
            'categoryId' => explode(',', $this->categoryId),
            'subCategoryId' => explode(',', $this->subCategoryId),
            'filterId' => explode(',', $this->filterId),
            'keywords' => explode(',', $this->keywords),
        ]);
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'keywords' => 'required',
            'timezone' => 'required',
            'about_business' => 'required|string|max:255',
            'phone1' => 'required|string',
            'phone2' => 'nullable|string',
            'website' => 'nullable',
            'email' => 'nullable|email',
            'city' => 'required',
            'lat' =>  'required',
            'lng' =>  'required',
            'region.*' => 'required|exists:regions,id',
            'country.*' => 'required|exists:countries,id',
            'council.*' => 'required|exists:councils,id',
            'division.*' => 'required|exists:divisions,id',
            'neigborhood.*' => 'required|exists:neighborhoods,id',
            'categoryId.*' => 'required|exists:categories,id',
            'subCategoryId.*' => 'required|exists:subcategories,id',
            'filterId.*' => 'required|exists:filters,id',
            'keywords.*' => 'required',
        ];
    }
}
