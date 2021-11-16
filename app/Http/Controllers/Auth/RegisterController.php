<?php

namespace App\Http\Controllers\Auth;

use App\Business;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\CompleteProfile;

class RegisterController extends Controller
{
  /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

  /**
   * Create a new user instance after a valid registration.
   *
   * @param  array  $data
   * @return \App\User
   */
  protected function create(RegisterRequest $request)
  {
    $data = [];
    $message = " ";
    $statusCode = 200;
    $name = $request->first_name . " " . $request->last_name;
    $user = User::create([
      'name'     => $name,
      'phone'    => $request->phone,
      'email'    => $request->email,
      'password' => $request->password
    ]);
    $data = [
      'user' => $user
    ];
    $OTP = $this->generateOTP();
    Cache::put([$request->phone => $OTP], now()->addMinutes(5));
    $content = urlencode('your OTP verification code is ' . $OTP);
    $feedback = sendSms($content, $request->phone);
    if ($feedback) {
      $message = "A verifiaction code ahs been sent to phone number. Please copy and palse below to complete registration ";
    } else {
      $message = "An error occured while sending a message. Click the resend button";
    }
    return apiResponse($data, $message, $statusCode);
  }

  public function resendOtp(Request $request)
  {
    $request->validate([
      'phone' => 'required|numeric'
    ]);
    $data = [];
    $message = "";
    $statusCode = 200;
    $isUser = User::where(['phone' => $request->phone])->first();
    if ($isUser) {
      if (Cache::get($request->phone)) {
        Cache::forget($request->phone);
      }
      $OTP = $this->generateOTP();
      Cache::put([$request->phone => $OTP], now()->addMinutes(5));
      $content = 'your OTP verification code is' . $OTP;
      $feedback = sendSms($content, $request->phone);
      if ($feedback) {
        $message = "A verifiaction code has been sent to phone number. Please copy and place below to complete registration ";
      } else {
        $message = "An error occured while sending a message. Click the resend button";
      }
    } else {
      $statusCode = 422;
      $message = "The Phone number placed is either of wrong format or is not found in the system ";
    }
    return apiResponse($data, $message, $statusCode);
  }

  private function generateOTP()
  {
    $otpCode = mt_rand(100000, 999999);
    return $otpCode;
  }

  public function completeProfile(CompleteProfile $request)
  {

    $data = [];
    $message = " ";
    $statusCode = 200;
    $user = User::find(auth()->user()->id);
    if ($user) {
      $user->update([
        'dob' => $request->dob,
        'gender' => $request->gender,
        'country' => $request->country,
        'city' => $request->city,
        'neighbor' => $request->neighbor,
        'lat' => $request->lat,
        'lng' => $request->lng,
        'region' => $request->region,
        'address' => $request->address,
      ]);
      $userAlbum = $user->userAlbum()->create([
        'name' => 'Profile Picture',
    ]);
    if (!empty($request->profile_picture)) {
      $path = "public/user/" . auth()->user()->id ."/album";
      $storePath = imageUpload($path,$request->profile_picture);
      $user->update(['profile_picture' => $storePath]);
      $userAlbum->userMedia()->create([
        'reference_type' => 'profile_picture',
        'reference_id' => $user->id,
        'media_type' => $request->profile_picture->getMimeType(),
        'media_url' => $storePath,
    ]);
  }

      $message = "Successfully updated";
      $data = [
        'user' => $user
      ];
    } else {
      $message = "This user does not exist in the system";
      $statusCode = 422;
    }
    return apiResponse($data, $message, $statusCode);
  }


  public function welcomeComplete(Request $request)
  {
    $data = [];
    $message = " ";
    $statusCode = 200;
    $user = User::find(auth()->user()->id);
    $user->update(['profile_complete' => true]);
    $message = "Profile Complete Successfully";
    $request->user()->currentAccessToken()->delete();
    $accessToken = $user->createToken('authToken')->plainTextToken;
    $data = [
      'accessToken' => $accessToken,
      'user' => $user,
    ];
    return apiResponse($data, $message, $statusCode);
  }
}
