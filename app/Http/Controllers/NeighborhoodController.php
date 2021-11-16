<?php

namespace App\Http\Controllers;

use App\Neighborhood;
use App\User;
use Illuminate\Http\Request;

class NeighborhoodController extends Controller
{
    public function show()
    {
        $data = [];
        $statusCode = 200;
        $message = "";
        $data = Neighborhood::select('id','name')->get();
        return apiResponse($data,$message,$statusCode);
    }

    public function showNeighborhoodFromCouncil(Request $request)
    {
        $data = [];
        $statusCode = 200;
        $message = "Neighborhood Listing Successful";
        $request->validate([
        'councilId' => 'required|integer|exists:neighborhoods,council_id',
    ]);
        $id = explode(',', $request->councilId);    
        $data = Neighborhood::where('council_id',$id)->select('id','name')->get();
        return apiResponse($data,$message,$statusCode);
    }

    public function userNeigborhood()
    {
        $data = [];
        $statusCode = 200;
        $message = "";
        $id = auth()->user()->id;
        $user = User::find($id);
        $data = Neighborhood::where('council_id',$user->council_id)->select('id','name')->get();
        return apiResponse($data,$message,$statusCode);
    }
}
