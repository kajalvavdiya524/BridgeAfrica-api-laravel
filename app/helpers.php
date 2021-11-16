<?php

use Carbon\Carbon;
use App\Mail\SendOtp;
use Illuminate\Support\Env;
use App\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('check_valid_date')) {
    function check_valid_date($input_date)
    {
        $date = false;
        try {
            if ($input_date !== '') {
                $delimiter = '';
                if (strpos($input_date, '/') !== false)
                    $delimiter = '/';
                if (strpos($input_date, '-') !== false)
                    $delimiter = '-';
                if ($delimiter !== '') {
                    $date = \DateTime::createFromFormat('d' . $delimiter . 'm' . $delimiter . 'Y', $input_date);
                }
            }
        } catch (\Exception $e) {
        }
        if ($date !== false)
            return Carbon::parse($date);
        return $date;
    }
}

if (!function_exists('is_serialized')) {
    function is_serialized($data)
    {
        return (@unserialize($data) !== false);
    }
}

/**
 * Method to send api response
 *
 * @param $data
 * @param $message
 * @param $statusCode
 * @param $meta
 * @return bool|string
 */
function apiResponse($data, $message = '', $statusCode = 200, $meta = '')
{
    $message = (is_array($message)) ? reset($message) : $message;

    $response['data'] =  ($data) ?? [];
    $response['message'] = (is_array($message)) ? $message[0] : $message;

    if (!empty($meta)) {
        $response = array_merge($response, $meta);
    }

    return response()->json($response, $statusCode);
}

/**
 * Method to send api response
 *
 * @param $content
 * @param $phone numner
 * @return $bool
 */
function sendSms($content, $phoneNumber)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => env('SMS_API_URL'),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "username=" . env('SMS_USERNAME') . "&password=" . env('SMS_PASSWORD') . "&msisdn=00237" . $phoneNumber . "&msg=" . $content,
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return false;
    } else {
        return true;
    }
}

function sendEmailOtp($email, $content)
{
    Mail::to($email)->send(new SendOtp($content));
    return (Mail::failures());
}

function verifyOtp($OTP, $media)
{
    if ($OTP == Cache::get($media)) {
        Cache::forget($media);
        return true;
    }
    return false;
}

/**
 * Method to Add Notification for users
 *
 * @param $userID
 * @param $referenceType
 * @param $referenceId
 * @param $message
 */
function addNotification($userID = NULL, $referenceType, $referenceId, $message, $businessID = NULL)
{
    $notification = new Notification();
    if($userID){
        $notification->user_id = $userID;
    }
    $notification->reference_type = $referenceType;
    $notification->reference_id = $referenceId;
    $notification->notification_text = $message;
    if($businessID){
        $notification->business_id = $businessID;
    }

    if ($notification->save()) {
        $data = [];
        $message = 'Notification added successfully.';
        return apiResponse($data, $message, 201);
    } else {
        $data = [];
        $message = 'Notification not added.';
        return apiResponse($data, $message, 500);
    }
}

function imageUpload($path, $file)
{
    if (!Storage::exists($path)) {
        Storage::makeDirectory($path);
    }
    $path = Storage::putFile($path, $file);
    return '/storage' . substr($path, 6);
}

function getMediaUrl($value)
{
    return env('APP_URL') . ':' . $_SERVER['SERVER_PORT'] . $value;
}

function deleteMedia($path)
{
    $path = Str::after($path, 'storage');
    $path =  "public" . $path;
    if (!Storage::exists($path)) {
        return false;
    }
    Storage::delete($path);
    return true;
}

function downloadMedia($path)
{
    $path = Str::after($path, 'storage');
    $path =  "public" . $path;
    if (!Storage::exists($path)) {
        return false;
    }
    $media = Storage::download($path);

    return $media;
}

function deleteDirectory($directory)
{
    if (!Storage::exists($directory)) {
        // path does not exist
        return false;
    } else {
        Storage::deleteDirectory($directory);
        return true;
    }
}

/**
 * Method to store images and files into to the Storage Folder
 *
 * @param $dirName
 * @param $file
 * @param $fileDetails
 * @return string|path
 */
function imageStorage($dirName, $file, $fileDetails)
{

    $DirPath = 'public/' . $dirName;
    if (!Storage::exists($DirPath)) {
        Storage::makeDirectory($DirPath);
    }
    $imageName = time() . '.' . $fileDetails->extension();
    $path = $file->storeAs($DirPath, $imageName);
    return $path;
}
