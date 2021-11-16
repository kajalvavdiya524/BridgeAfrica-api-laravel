<?php

namespace App\Http\Controllers;

use App\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function show()
    {
        $data = [];
        $statusCode = 200;
        $message = "";
        $data = Division::select('id','name')->get();
        return apiResponse($data,$message,$statusCode);
    }

    public function showDivisionFromRegion(Request $request)
    {
        $data = [];
        $statusCode = 200;
        $message = "Division Listing Successful";
        $request->validate([
        'regionId.*' => 'required|integer|exists:divisions,region_id',
    ]);
        $id = explode(',', $request->regionId);    
        $data = Division::whereIn('region_id',$id)->select('id','name')->get();
        return apiResponse($data,$message,$statusCode);
    }
}
