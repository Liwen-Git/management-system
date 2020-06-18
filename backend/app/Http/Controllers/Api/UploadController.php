<?php

namespace App\Http\Controllers\Api;


use App\Result;

class UploadController extends Controller
{
    public function uploadImage()
    {
        $file = request()->file('file');

        // 上传
        $path = $file->store('image/'. auth()->user()->id, 'api');

        $url = asset('api/'. $path);

        return Result::success([
            'path'=> $path,
            'url' => $url,
        ]);
    }
}
