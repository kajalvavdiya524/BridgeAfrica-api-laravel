<?php

namespace App\Http\Controllers;

use App\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function show()
    {
        $data = [];
        $statusCode = 200;
        $message = "";
        $data = Region::select('id','name')->get();
        return apiResponse($data,$message,$statusCode);
    }

    public function showRegionFromCountry(Request $request)
    {
        $data = [];
        $statusCode = 200;
        $message = "";
        $request->validate([
        'countryId.*' => 'required|integer|exists:regions,country_id',
    ]);
        $id = explode(',', $request->countryId);    
        $data = Region::whereIn('country_id',$id)->select('id','name')->get();
        return apiResponse($data,$message,$statusCode);
    }
}
