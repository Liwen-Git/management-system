<?php


namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\Storage;
use Vtiful\Kernel\Excel;

class ExportController extends Controller
{
    public function exportExcel(array $data, $name, $savePath = 'excel_export')
    {
        Storage::disk('api')->makeDirectory($savePath);

        $config = ['path' => storage_path('app/api/'. $savePath)];
        $name = uniqid().$name;
        $excel = new Excel($config);

        $filePath = $excel->fileName($name)
            ->header(['Item', 'Cost'])
            ->data($data)
            ->output();

        $url = asset('api/'. $savePath . '/'. basename($filePath));
        dd($url);
    }

    public function exportDemo()
    {
        $data = [
            ['Rent', 1000],
            ['Gas', 100],
            ['Good', 2000]
        ];
        $name = 'xx.xlsx';

        $this->exportExcel($data, $name);
    }
}
