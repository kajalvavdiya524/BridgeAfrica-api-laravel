<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessIdentityCreate extends FormRequest
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
            'name' => 'required', 'string',
            'logo_path' => 'nullable', 'mimes:jpeg,bmp,png',
            'keywords' => 'required',
            'language' => 'string',
            'about_business' => 'string',
            'lat' =>  'required',
            'lng' =>  'required',
            'address' => 'required|string|max:10',
            'city' => 'required|string',
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'country.*.exists' => ':attribute does not exist',
            'region.*.exists' => ':attribute does not exist',
            'division.*.exists' => ':attribute does not exist',
            'council.*.exists' => ':attribute does not exist',
            'neigborhood.*.exists' => ':attribute does not exist',
            'categoryId.*.exists' => ':attribute does not exist',
            'subCategoryId.*.exists' => ':attribute does not exist',
            'filterId.*.exists' => ':attribute does not exist',
        ];
    }

    public function attributes()
    {
        $attributes = array();
        foreach ($this->country as $index => $country) {
            $attributes['country.' . $index] = "country id " . $country;
        }
        foreach ($this->region as $index => $region) {
            $attributes['region.' . $index] = "region id " . $region;
        }
        foreach ($this->division as $index => $division) {
            $attributes['division.' . $index] = "division id " . $division;
        }
        foreach ($this->council as $index => $council) {
            $attributes['council.' . $index] = "council id " . $council;
        }
        foreach ($this->neigborhood as $index => $neigborhood) {
            $attributes['neigborhood.' . $index] = "neigborhood id " . $neigborhood;
        }
        foreach ($this->categoryId as $index => $categoryId) {
            $attributes['categoryId.' . $index] = "categoryId id " . $categoryId;
        }
        foreach ($this->subCategoryId as $index => $subCategoryId) {
            $attributes['subCategoryId.' . $index] = "subCategoryId id " . $subCategoryId;
        }
        foreach ($this->filterId as $index => $filterId) {
            $attributes['filterId.' . $index] = "filterId id " . $filterId;
        }
        return $attributes;
    }
}
