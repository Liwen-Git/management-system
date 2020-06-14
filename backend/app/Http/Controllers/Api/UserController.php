<?php

namespace App\Http\Controllers\Api;


use App\Exceptions\BaseResponseException;
use App\Http\Modules\User;
use App\Result;
use App\ResultCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function add()
    {
        $this->validate(request(), [
            'name' => 'required|unique:users',
            'email' => 'email',
            'role_id' => 'required|integer',
            'password' => 'required|between:6,30|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);

        $user = new User();
        $user->name = request('name');
        $user->email = request('email');
        $user->password = Hash::make(request('password'));
        $user->role_id = request('role_id');
        $user->is_super = 0;
        $user->save();

        return Result::success($user);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ValidationException
     */
    public function edit()
    {
        $this->validate(request(), [
            'id' => 'required|integer',
            'name' => 'required',
            'email' => 'email',
            'role_id' => 'required|integer',
        ]);

        $check = User::where('id', '<>', request('id'))
            ->where('name', request('name'))
            ->count();
        if ($check > 0) {
            throw new BaseResponseException('用户名重复', ResultCode::PARAMS_INVALID);
        }

        $user = User::find(request('id'));
        $user->name = request('name');
        $user->email = request('email');
        $user->role_id = request('role_id');
        $user->save();

        return Result::success($user);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function list()
    {
        $pageSize = request('page_size', 10);

        $res = User::with('role')
            ->where('is_super', '<>', User::SUPER_USER)
            ->orderBy('id', 'desc')
            ->paginate($pageSize);

        return Result::success([
            'list' => $res->items(),
            'total' => $res->total(),
        ]);
    }



    /**
     * 删除用户
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ValidationException
     */
    public function delete()
    {
        $this->validate(request(), [
            'id' => 'required'
        ]);

        User::destroy(request('id'));

        return Result::success();
    }

    /**
     * 修改密码
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ValidationException
     */
    public function changePassword()
    {
        $this->validate(request(), [
            'password' => 'required|between:6,30|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);

        /** @var User $user */
        $user = auth()->user();
        $user->password = Hash::make(request('password'));
        $user->save();

        return Result::success($user);
    }
}
