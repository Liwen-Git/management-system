<?php

namespace App\Http\Controllers\Api;


use App\Exceptions\BaseResponseException;
use App\Result;
use App\ResultCode;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login()
    {
        try {
            $this->validate(request(), [
                'key' => 'required',
                'captcha' => 'required|captcha_api:'. request('key')
            ]);
        } catch (ValidationException $exception) {
            throw new BaseResponseException('验证码错误', ResultCode::PARAMS_INVALID);
        }
        $credentials = request(['name', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            throw new BaseResponseException('登录失败', ResultCode::UNLOGIN);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->logout();

        return Result::success('Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    protected function respondWithToken($token)
    {
        return Result::success([
            'token' => 'bearer '. $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
