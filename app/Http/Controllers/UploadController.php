<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{

    public function upload(Request $request){

        $filename = $request->file('media')->path();
        $mime = mime_content_type($filename);

        if(strstr($mime, "video/")){

            $type = 'video';

            $path = 'public/media/videos';

            $content = $request->file('media')->store($path);
            $content = strval($content);

        }else if(strstr($mime, "image/")){

            $type = 'image';

            $path = 'public/media/photos';

            $content = $request->file('media')->store($path);
            $content = strval($content);

        }
        $data = [
            'link' => $content
        ];
        $message = 'your media has been uploaded';

        return apiResponse($data,$message,200);

    }

}
