<?php

namespace App\Http\Controllers\Api;


use App\Exceptions\BaseResponseException;
use App\Http\Modules\Permission;
use App\Http\Modules\Role;
use App\Http\Modules\RolePermission;
use App\Result;
use App\ResultCode;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function list()
    {
        $list = Role::all();

        return Result::success($list);
    }

    /**
     * 新增角色 和 角色权限关联
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function add()
    {
        $this->validate(request(), [
            'name' => 'required|string',
            'permission_id_arr' => 'required|array'
        ]);

        if (request('name') == 'super_admin') {
            throw new BaseResponseException('不能使用该名称', ResultCode::PARAMS_INVALID);
        }

        DB::beginTransaction();
        try {
            $role = Role::create(request()->except(['id', 'permission_id_arr']));

            $permissionIdArr = request('permission_id_arr');
            $this->insertRolePermission($role, $permissionIdArr);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new BaseResponseException('新增角色失败:'.$exception->getMessage(), ResultCode::DB_INSERT_FAIL);
        }
        DB::commit();

        return Result::success($role);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function edit()
    {
        $this->validate(request(), [
            'id' => 'required|integer'
        ]);
        $id = request('id');

        if (request('name') == 'super_admin') {
            throw new BaseResponseException('不能使用该名称', ResultCode::PARAMS_INVALID);
        }

        DB::beginTransaction();
        try {
            $role = Role::find($id);
            $role->update(request()->all(['name', 'description']));

            $permissionIdArr = request('permission_id_arr');
            RolePermission::where('role_id', $id)->delete();
            $this->insertRolePermission($role, $permissionIdArr);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new BaseResponseException('编辑角色失败:'.$exception->getMessage(), ResultCode::DB_UPDATE_FAIL);
        }
        DB::commit();

        return Result::success($role);
    }

    /**
     * 插入 角色权限关联表
     * @param Role $role
     * @param array $permissionIdArr
     */
    public function insertRolePermission(Role $role, array $permissionIdArr)
    {
        if (!empty($permissionIdArr)) {
            $permissionArr = Permission::whereIn('id', $permissionIdArr)->get()->keyBy('id');
            $insert = [];
            foreach ($permissionIdArr as $item) {
                $permission = $permissionArr[$item];
                $insert[] = [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                    'permission_id' => $item,
                    'permission_name' => $permission->name,
                    'permission_url' => $permission->url,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            RolePermission::insert($insert);
        }
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete()
    {
        $this->validate(request(), [
            'id' => 'required|integer'
        ]);
        $id = request('id');

        DB::beginTransaction();
        try {
            Role::destroy($id);
            RolePermission::where('role_id', $id)->delete();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new BaseResponseException('删除角色失败:'.$exception->getMessage(), ResultCode::DB_DELETE_FAIL);
        }
        DB::commit();

        return Result::success();
    }
}
