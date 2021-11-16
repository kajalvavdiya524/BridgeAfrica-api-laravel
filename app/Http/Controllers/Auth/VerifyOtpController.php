<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;

class VerifyOtpController extends Controller
{
    protected function verifyOtp(Request $request,User $user){
        $request->validate([
            'OTP' => 'required',
            'phone' => 'required'
        ]);
        $data = [];
        $message = " ";
        if($user->phone == $request->phone){
            if($request->OTP == Cache::get($request->phone) || $request->OTP == env('OTP_BY_PASS')){
                Cache::forget($request->phone);
                Auth::login($user);
                $user->update(['verified_at'=> Carbon::now()->addHours(1)->format('H:i:m')]);
                $data =[
                    'accessToken' => auth()->user()->createToken('authToken')->plainTextToken, 
                    'user' => auth()->user(),
                ];
                $message = "Verification Successful. You are now logged in";
                return apiResponse($data,$message,200);
            }
            $message = "The verification code is wrong or has expired. Click the RESEND button";
                return apiResponse($data,$message,422);
        }else{
            $message = "The number is incorrect or does not belong the this user";
                return apiResponse($data,$message,422); 
        }
        
    }
}
