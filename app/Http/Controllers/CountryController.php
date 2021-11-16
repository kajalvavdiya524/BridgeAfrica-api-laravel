<?php

namespace App\Http\Controllers;

use App\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function show()
    {
        $data = [];
        $statusCode = 200;
        $message = "Country Listing Successful";
        $data = Country::select('id','name')->get();
        return apiResponse($data,$message,$statusCode);
    }
}
