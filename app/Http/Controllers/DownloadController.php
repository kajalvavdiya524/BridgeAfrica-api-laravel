<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{

    public function download(Request $request){

        $file_name = $request->file_name;

        $media = Storage::download($file_name);

        return $media;
    }


}
