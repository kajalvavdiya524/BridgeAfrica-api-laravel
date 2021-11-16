<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request){
        $data = [];
        $message = "Logout successfully";
        $request->user()->currentAccessToken()->delete();
        Auth::guard('web')->logout();        
        return apiResponse($data,$message,200);
     }
}
