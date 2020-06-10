<?php


namespace App;


use Illuminate\Contracts\Support\Arrayable;

class Result
{
    /**
     * @param string $message
     * @param array $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public static function success($message = '请求成功', $data = []) {
        if(is_array($message) or $message instanceof Arrayable){
            $data    = $message;
            $message = '请求成功';
        }
        return response([
            'code'    => ResultCode::SUCCESS,
            'message' => $message,
            'data'    => $data,
            'timestamp' => time(),
        ]);
    }

    /**
     * @param $code
     * @param $message
     * @param array $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public static function error($code, $message, $data = [])
    {
        return response([
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
            'timestamp' => time(),
        ]);
    }
}