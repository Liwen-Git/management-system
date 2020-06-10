<?php


namespace App\Http\Controllers\Api;


use App\Http\Modules\Role;
use App\Http\Modules\RolePermission;
use App\Http\Modules\User;
use App\Result;

class SelfController extends Controller
{
    /**
     * 验证码
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function captcha()
    {
        $img = app('captcha')->create('default', true);

        return Result::success($img);
    }

    /**
     * 获取 用户名 角色名 用户权限数组
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function userInfo()
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->is_super === 1) {
            $roles = ['super_admin'];
            $permissions = [];
        } else {
            $role = Role::find($user->role_id);
            $roles = [$role->name];
            $permissions = RolePermission::where('role_id', $user->role_id)->pluck('permission_url')->all();
        }

        return Result::success([
            'roles' => $roles,
            'name' => $user->name,
            'permissions' => $permissions
        ]);
    }
}