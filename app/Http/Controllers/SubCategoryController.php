<?php

namespace App\Http\Controllers;

use App\Subcategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function show()
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        $data = Subcategory::select('id', 'name')->get();
        return apiResponse($data, $message, $statusCode);
    }

    public function subCategoriesList(Request $request)
    {
        $request->validate([
            'categoryId.*' => 'required|exists:subcategories,cat_id',
        ]);

        $id = explode(',', $request->categoryId);
        $data = Subcategory::whereIn('cat_id', $id)->select('id', 'name')->get()
            ->map(function ($item) {
                $data = [
                    'sub_cat_id' => $item->id,
                    'subcategory' => $item->name,
                    'filters' => $item->filters()->select('id', 'name')->get()
                ];
                return $data;
            });
        $message = 'Sub category Listing successfuly.';
        return apiResponse($data, $message, 200);
    }

    public function businessCategory(Request $request)
    {
        $request->validate([
            'categoryId.*' => 'required|exists:subcategories,cat_id',
        ]);

        $id = explode(',', $request->categoryId);
        $data = Subcategory::whereIn('cat_id', $id)->select('id', 'name')->get()
            ->map(function ($item) {
                $data = [
                    'sub_cat_id' => $item->id,
                    'subcategory' => $item->name,
                    'filters' => $item->filters()->select('id', 'name')->get()
                ];
                return $data;
            });
        $message = 'Sub category Listing successfuly.';
        return apiResponse($data, $message, 200);
    }
}
