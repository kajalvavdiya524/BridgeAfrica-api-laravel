<?php

namespace App\Http\Controllers;

use App\Council;
use Illuminate\Http\Request;

class CouncilController extends Controller
{
    public function show()
    {
        $data = [];
        $statusCode = 200;
        $message = "";
        $data = Council::select('id','name')->get();
        return apiResponse($data,$message,$statusCode);
    }

    public function showCouncilFromDivision(Request $request)
    {
        $data = [];
        $statusCode = 200;
        $message = "Council Listing Successful";
        $request->validate([
        'divisionId.*' => 'required|integer|exists:councils,division_id',
    ]);
    $id = explode(',', $request->divisionId);    
    $data = Council::whereIn('division_id',$id)->select('id','name')->get();
        return apiResponse($data,$message,$statusCode);
    }
}
