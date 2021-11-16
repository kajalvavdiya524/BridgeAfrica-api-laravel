<?php

namespace App\Http\Controllers;

use App\User;

class PeopleAroundController extends Controller
{
  public function getPeopleNear($latitude, $longitude, $radius = 50)
  {
    $people = User::select('users.id')
      ->selectRaw('(69.0
                       * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(?))
                       * COS(RADIANS(users.lat))
                       * COS(RADIANS(? - users.lng))
                       + SIN(RADIANS(?))
                       * SIN(RADIANS(users.lat)))))
                               ) AS distance', [$latitude, $longitude, $latitude])
      ->whereRaw('users.lat BETWEEN ? -(? / 69.0) and ? + (? / 69.0)
              and users.lng BETWEEN ? -(? / (69.0 * COS(RADIANS(?)))) 
              and ? + (? / (69.0 * COS(RADIANS(?))))
              and id != ?', [
        $latitude, $radius, $latitude, $radius,
        $longitude, $radius, $latitude, $longitude, $radius, $latitude, auth()->user()->id
      ])
      ->havingRaw("distance < ?", [$radius])
      ->orderBy('distance', 'asc')
      ->limit(10)
      ->get();


    return $people;
  }

  public function getPeopleData()
  {
    $data = [];
    $message = " ";
    $statusCode = 200;
    $id = $this->getPeopleNear(auth()->user()->lat, auth()->user()->lng, 50);
    if (count($id) != 0) {
      $num = [];
      foreach ($id as $key => $val) {
        $num[$key] = $val->id;
      }
      $data = User::whereIn('id', $num)->where('id', '<>', auth()->user()->id)->get()->map(function ($item, $key) {
        $data = [
          'id' => $item->id,
          'profile_picture' => $item->profile_picture,
          'name' => $item->name,
          'followers' => $item->userfollower->count(),
        ];
        return $data;
      });
    } else {
      $data = User::take(3)->where('id', '<>', auth()->user()->id)->get()->map(function ($item, $key) {
        $data = [
          'id' => $item->id,
          'profile_picture' => $item->profile_picture,
          'name' => $item->name,
          'followers' => $item->userfollower->count(),
        ];
        return $data;
      });
    }
    return apiResponse($data, $message, $statusCode);
  }
}
