<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\User;
use Exception;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
class SocialAuthController extends Controller
{
    
    public function callBack($provider){
        $data = [];
        $message = "Logged in Successfully";
        $code = 200;
        $userSocial = Socialite::with($provider)->stateless()->user();
        $isUser = User::where(['provider_id'=>$userSocial->getId(),'provider'=>$provider])->first();
        if(!$isUser){
            $isUser = User::create([
            'provider' =>$provider,
            'provider_id' =>$userSocial->getId(),
            'name' => $userSocial->getName(),       
            'email' => $userSocial->getEmail(),
            'verified_at'=>Carbon::now()
            ]);
            $message = "Registered Successfully";
            $code = 201;
        }
        Auth::login($isUser);
        auth()->user()->update(['status' => true]);
        $data = [
            'accessToken' => auth()->user()->createToken('authToken')->plainTextToken,
            'user' => auth()->user(),
        ];
        return apiResponse($data,$message,$code);
    }
}
