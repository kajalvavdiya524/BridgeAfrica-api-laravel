<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function Login(Request $request){

        $request->validate([
            'password' => 'required|string',
            'email' => 'required|email',
        ]);
        $data = [];
        $messsage = " ";
        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $data = $request->all();
            $messsage = "Password Or Email Incorrect";
            return apiResponse($data,$messsage,422);
        }
        auth()->user()->update(['status' => true]) ;
        $accessToken = auth()->user()->createToken('authToken')->plainTextToken;  
        $data=[
            'accessToken'=>$accessToken,
            'user' => auth()->user(),
        ];
        $messsage = "Login Successful";
        return apiResponse($data,$messsage,200);   
     }
    
}
