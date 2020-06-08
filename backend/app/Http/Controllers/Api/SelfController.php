<?php


namespace App\Http\Controllers\Api;


class SelfController extends Controller
{
    public function captcha()
    {
        $url = captcha_src();
    }
}