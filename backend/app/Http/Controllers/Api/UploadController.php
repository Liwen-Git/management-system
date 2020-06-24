<?php

namespace App\Http\Controllers\Api;


use App\Result;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadController extends Controller
{
    /**
     * 图片上传
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function uploadImage()
    {
        $file = request()->file('file');

        $content = file_get_contents($file->getRealPath());
        $savePath = 'image/'. auth()->user()->id;
        $extension = $file->getClientOriginalExtension();

        $nameArr = self::makeName($content, $extension, $savePath);
        if (!$nameArr['status']) {
            // 上传
            $path = $file->storeAs($savePath, $nameArr['name'], 'api');
        } else {
            $path = $savePath . '/'. $nameArr['name'];
        }

        $url = asset('api/'. $path);
        $image = Image::make($url);

        return Result::success([
            'path'=> $path,
            'url' => $url,
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
            'size' => $file->getSize(),
        ]);
    }

    /**
     * @param $content  --文件数据流
     * @param $extension  --文件后缀名
     * @param string $path --文件存储路径
     * @return array
     */
    public static function makeName($content, $extension, $path = '')
    {
        $name = md5($content) . '.'. $extension;
        $disk = Storage::disk('api');
        $status = $disk->exists($path. $name);

        return ['status' => $status, 'name' => $name];
    }

    public function uploadFile()
    {
        $file = request()->file('file');
        $dir = request('dir', 'file');

        $savePath = $dir . '/'. auth()->user()->id;
        $name = uniqid() . $file->getClientOriginalName();

        $path = $file->storeAs($savePath, $name, 'api');
        $url = $url = asset('api/'. $path);

        return Result::success([
            'url' => $url,
            'name' => $name,
            'size' => $file->getSize()
        ]);
    }
}
