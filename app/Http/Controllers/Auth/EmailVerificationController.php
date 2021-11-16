<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class EmailVerificationController extends Controller
{
    public function emailVerification(Request $request){
        $request->validate([
            'email' => 'required|email'
        ]);
        $data = [];
        $message ="";
        $statusCode = 200;
        $otp = $this->generateOTP();
        Cache::put([$request->email => $otp],now()->addMinutes(5));
        $feedback = sendEmailOtp($request->email,$otp);
        if($feedback != 0) {
          $message = "A Verificatioin Code has been sent to your Mail".$request->email."Copy and place in the box below";
        }

      else {
        $message = "A Verificatioin Code could not be sent to your Mail".$request->email."Please click the resend button";
        $statusCode = 422;
      }  
      return apiResponse($data,$message,$statusCode); 
    }

    private function generateOTP(){
        $otpCode = mt_rand(100000,999999);
        return $otpCode;
    }

    protected function verifyEmailOtp(Request $request,User $user){
        $request->validate([
            'OTP' => 'required|numeric',
            'email' => 'required|email'
        ]);
        $data = [];
        $message ="";
        $statusCode = 200;
        if(verifyOtp($request->OTP,$request->email)){
          Auth::login($user);
            auth()->user()->update(['verified_at'=>Carbon::now()]);
            $data =[
                'accessToken' => auth()->user()->createToken('authToken')->plainTextToken, 
                'user' => auth()->user(),
            ];
            $message = "A Verificatioin Successful";
            return apiResponse($data,$message,$statusCode);
        }
        // if($request->OTP == Cache::get($request->email)){
        //     Cache::forget($request->email);
        //     Auth::login($user);
        //     auth()->user()->update(['verified_at'=>Carbon::now()]);
        //     $data =[
        //         'accessToken' => auth()->user()->createToken('authToken')->plainTextToken, 
        //         'user' => auth()->user(),
        //     ];
        //     $message = "A Verificatioin Successful";
        //     return apiResponse($data,$message,$statusCode);
        // }
        $message = "The Code is wrong or has expired";
        $statusCode = 422;
        return apiResponse($data,$message,$statusCode);
    }

    protected function resendEmailOtp(Request $request){
        $request->validate([
            'email' => 'required|email'
        ]);
        $data = [];
        $message ="";
        $statusCode = 200;
        if(Cache::get($request->email)){
          Cache::forget($request->email);
        }
        $otp = $this->generateOTP();
        Cache::put([$request->email => $otp],now()->addMinutes(5));
        $feedback = sendEmailOtp($request->email,$otp);
        if($feedback != 0) {
          $message = "A Verificatioin Code has been sent to your Mail".$request->email;
        }

      else {
        $message = "A Verificatioin Code could not be sent to your Mail".$request->email."Please click the resend button";
        $statusCode = 422;
      }  
      return apiResponse($data,$message,$statusCode); 
    }
}
