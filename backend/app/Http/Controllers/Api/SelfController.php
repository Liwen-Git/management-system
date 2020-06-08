<?php


namespace App\Http\Controllers\Api;


use App\Result;

class SelfController extends Controller
{
    public function captcha()
    {
        $url = captcha_src();

        return Result::success(['url' => $url]);
    }
}