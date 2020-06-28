<?php

namespace App\Http\Controllers\Api;


use App\Result;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Vtiful\Kernel\Excel;

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
        $savePath = 'image';
        $extension = $file->getClientOriginalExtension();

        $nameArr = self::makeName($content, $extension, $savePath);
        if (!$nameArr['status']) {
            // 上传
            $path = $file->storeAs($savePath, $nameArr['name'], 'api');
        } else {
            $path = $savePath . '/'. $nameArr['name'];
        }

        $url = asset('api/'. $path);
        // Image::make($url) 在laradock中报错：Unable to init from given url
        $image = Image::make(storage_path('app/api/'. $path));

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
        $status = $disk->exists($path. '/'. $name);

        return ['status' => $status, 'name' => $name];
    }

    /**
     * 文件上传
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function uploadFile()
    {
        $file = request()->file('file');
        $savePath = request('dir', 'file');

        $name = uniqid() . $file->getClientOriginalName();

        $path = $file->storeAs($savePath, $name, 'api');
        $url = asset('api/'. $path);

        return Result::success([
            'url' => $url,
            'name' => $name,
            'size' => $file->getSize()
        ]);
    }

    /**
     * excel 文件上传 文件保存并解析
     */
    public function uploadAndReadExcel()
    {
        $file = request()->file('file');
        $savePath = request('dir', 'excel_import');

        $name = uniqid() . $file->getClientOriginalName();

        $file->storeAs($savePath, $name, 'api');

        $config = ['path' => storage_path('app/api/'. $savePath)];
        $excel = new Excel($config);

        $data = $excel->openFile($name)
            ->openSheet()
            ->getSheetData();

        /**
         array:4 [
            0 => array:3 [
                0 => "a"
                1 => "b"
                2 => "c"
            ]
            1 => array:3 [
                0 => 11
                1 => 22
                2 => 33
            ]
            2 => array:3 [
                0 => 111
                1 => 222
                2 => 333
            ]
         ]
         */
        dd($data);
    }
}
