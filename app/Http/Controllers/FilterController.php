<?php

namespace App\Http\Controllers;
use App\Filter;

use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function show()
    {
        $data = [];
        $message = "Filter listing Successful";
        $statusCode = 200;
        $data = Filter::select('id','name')->get();
        return apiResponse($data, $message, $statusCode);
    }
}
