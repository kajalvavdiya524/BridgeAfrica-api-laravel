<?php

namespace App\Http\Controllers;

use App\Business;
use App\Traits\Transformer;

class BusinessAroundController extends Controller
{
  public function getBusinessNear($latitude, $longitude, $radius = 50)
  {
    $business = Business::select('businesses.id')
      ->selectRaw('(69.0
                      * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(?))
                      * COS(RADIANS(businesses.lat))
                      * COS(RADIANS(? - businesses.lng))
                      + SIN(RADIANS(?))
                      * SIN(RADIANS(businesses.lat)))))
                              ) AS distance', [$latitude, $longitude, $latitude])
      ->whereRaw('businesses.lat BETWEEN ? -(? / 69.0) and ? + (? / 69.0)
             and businesses.lng BETWEEN ? -(? / (69.0 * COS(RADIANS(?)))) 
             and ? + (? / (69.0 * COS(RADIANS(?))))
             and id != ?', [
        $latitude, $radius, $latitude, $radius,
        $longitude, $radius, $latitude, $longitude, $radius, $latitude,auth()->user()->id
      ])
      ->havingRaw("distance < ?", [$radius])
      ->orderBy('distance', 'asc')
      ->limit(5)
      ->get();
    return $business;
  }

  public function getBusinessData()
  {
    $data = [];
    $message = " ";
    $statusCode = 200;
    $id = $this->getBusinessNear(auth()->user()->lat, auth()->user()->lng, 50);
    if (count($id) != 0) {
      $num = [];
      foreach ($id as $key => $val) {
        $num[$key] = $val->id;
      }
      $business = Business::whereIn('id', $num)->get();
      $data = Transformer::businesses($business);
    } else {
      $business = Business::take(3)->where('id', '<>', auth()->user()->id)->get();
      $data = Transformer::businesses($business);
    }
    return apiResponse($data, $message, $statusCode);
  }
}
