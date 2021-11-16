<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    private function generateOTP()
    {
        $otpCode = mt_rand(100000, 999999);
        return $otpCode;
    }

    public function passwordResetOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users,phone'
        ]);
        $message = "";
        $data = [];
        $statusCode = 200;
        $OTP = $this->generateOTP();
        Cache::put([$request->phone => $OTP], now()->addMinutes(5));
        $content = "Your Verification code is" . $OTP;
        $feedback = sendSms($content, $request->phone);
        $user = User::where('phone',$request->phone)->first();
        if ($feedback) {
            $message = "A verification code has being sent successfully to phone number. Copy and place in the box below";
        } else {
            $message = "A verification code could not be sent try click the Resend button";
        }
        $data = [
            'user' => $user
        ];
        return apiResponse($data, $message, $statusCode);
    }

    public function resetPassword(Request $request, User $user)
    {
        $message = "";
        $data = [];
        $statusCode = 200;
        $request->validate([
            'password' => ['required', 'string', 'confirmed']
        ]);
        if ($user) {
            $user->update(['password' => $request->password]);
            $message = "Password Successfuly changed";
        } else {
            $statusCode = 422;
            $message = "User does not exist";
        }
        return apiResponse($data, $message, $statusCode);
    }


    public function verifyPasswordResetOtp(Request $request)
    {
        $request->validate([
            'OTP' => ['required', 'numeric'],
            'phone' => ['required', 'numeric']
        ]);
        $message = "";
        $data = [];
        $statusCode = 200;
        if (verifyOtp($request->OTP, $request->phone) || $request->OTP == env('OTP_BY_PASS')) {
            Cache::forget($request->phone);
            $message = "Correct";
            return apiResponse($data, $message, $statusCode);
        } else {
            $message = "The Code is incorrrect";
            $statusCode = 422;
            return apiResponse($data, $message, $statusCode);
        }
    }

    public function resendPasswordOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users,phone'
        ]);
        $message = "";
        $data = [];
        $statusCode = 200;
        if (Cache::get($request->phone)) {
            Cache::forget($request->phone);
        }
        $OTP = $this->generateOTP();
        Cache::put([$request->phone => $OTP], now()->addMinutes(5));
        $content = "Your Verification code is: " . $OTP;
        $feedback = sendSms($content, $request->phone);
        if ($feedback) {
            $message = "A verification code has being sent successfully to phone number. Copy and place in the box below";
        } else {
            $message = "A verification code could not be sent try click the Resend button";
        }
        return apiResponse($data, $message, $statusCode);
    }
}
