<?php


namespace App\Http\Controllers\Api;


use App\Result;

class SelfController extends Controller
{
    public function captcha()
    {
        $img = app('captcha')->create('default', true);

        return Result::success($img);
    }
}