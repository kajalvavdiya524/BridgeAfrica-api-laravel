<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Business;
use App\Filter;
use App\Category;
use App\Traits\Transformer;

class BusinessSearchController extends Controller
{
  public function __construct()
  {
    $this->limit = config()->get('app.pagination');
  }

  /**
   *  display catergories with subcateries
   *
   * @return \Illuminate\Http\Response
   */
  public function displaySearchParam()
  {
    $data = Category::all()->map(function ($category, $key) {
      $data = [
        'id' => $category->id,
        'category' => $category->name,
        'sub_category' => $category->subCategory()->select('id', 'name', 'cat_image')->get()
      ];
      return $data;
    });
    $message = 'Category Listing successfuly.';
    return apiResponse($data, $message, 200);
  }

  /**
   *  display filters of a category
   *
   * @return \Illuminate\Http\Response
   */
  public function filter(Request $request, $id)
  {
    $request->merge(['cat_id' => $id]);
    $request->validate([
      'cat_id' => 'required|integer|exists:sub_categories,id',
    ]);
    $data = Filter::where('sub_category_id', $id)->select('id', 'name')->get();
    $message = 'Category Filter Listing successfuly.';
    return apiResponse($data, $message, 200);
  }

  /**
   *  display search results with respect to keyword
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  @page
   * @return \Illuminate\Http\Response
   */
  public function searchKeyword(Request $request, $page = 0)
  {
    $data = [];
    $message = ' ';
    $statusCode = 200;
    $request->merge(['page' => $page]);
    $request->validate([
      'keyword' => 'required|string',
      'page' => 'integer'
    ]);
    $keyword = $request->keyword;
    $data = Business::whereHas('keywords', function ($query) use ($keyword) {
      $query->where('name', 'LIKE', "%$keyword%");
    })->orWhere('name', 'LIKE', "%$keyword%")
      ->get();
    return apiResponse($data, $message, 200);
  }

  /**
   *  display search results
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function search(Request $request)
  {
    $request->validate([
      'categoryId' => 'nullable|numeric',
      'subCategoryId' => 'nullable|numeric',
      'filterId' => 'nullable|numeric',
      'neighborhoodId' => 'nullable|numeric',
      'distance' => 'nullable|integer',
    ]);
    $data = [];
    $message = ' ';
    $statusCode = 200;
    if ($request->categoryId) {
      $query = Business::join('business_category as bc', 'bc.business_id', '=', 'businesses.id')
        ->where('bc.category_id', $request->categoryId);
      if (!empty($request->subCategoryId)) {
        $query =  $query->join('business_subcategory as bsc', 'bsc.business_id', '=', 'businesses.id')
          ->where('bsc.subcategory_id', $request->subCategoryId);
      }
      if (!empty($request->filterId)) {
        $query =  $query->join('business_filter as busfil', 'busfil.business_id', '=', 'businesses.id')
          ->where('busfil.filter_id', $request->filterId);
      }
      if (!empty($request->neighborhoodId)) {
        $query =  $query->join('business_neighborhood as busNeigbor', 'busNeigbor.business_id', '=', 'businesses.id')
          ->where('busNeigbor.neighborhood_id', $request->neighborhoodId);
      }
      if (!empty($request->distance)) {
        $latitude = auth()->user()->lat;
        $longitude = auth()->user()->lng;
        $radius = $request->distance;
        $query = $query->select('businesses.*')->selectRaw('(69.0
      * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(?))
      * COS(RADIANS(businesses.lat))
      * COS(RADIANS(? - businesses.lng))
      + SIN(RADIANS(?))
      * SIN(RADIANS(businesses.lat)))))
              ) AS distance', [$latitude, $longitude, $latitude])
          ->whereRaw('businesses.lat BETWEEN ? -(? / 69.0) and ? + (? / 69.0)
      and businesses.lng BETWEEN ? -(? / (69.0 * COS(RADIANS(?)))) 
      and ? + (? / (69.0 * COS(RADIANS(?))))', [
            $latitude, $radius, $latitude, $radius,
            $longitude, $radius, $latitude, $longitude, $radius, $latitude
          ])
          ->havingRaw("distance < ?", [$radius])
          ->orderBy('distance', 'asc');
      }
      $business = $query->paginate($this->limit);
    } else {
      $business = Business::where('is_sponsored', true)->get()
        ->sortByDesc('followers')->values()->all();
    }
    $meta = Transformer::transformCollection($business);
    $business = $business->sortByDesc('followers')->values()->all();
    $data = Transformer::businesses($business);
    return apiResponse($data, $message, $statusCode, $meta);
  }
}
