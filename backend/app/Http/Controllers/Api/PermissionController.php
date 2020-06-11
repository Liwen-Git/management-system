<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BaseResponseException;
use App\Http\Modules\Permission;
use App\Http\Modules\RolePermission;
use App\Result;
use App\ResultCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function list()
    {
        $list = Permission::all()->toArray();
        $tree = listToTree($list);

        return Result::success($tree);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function add()
    {
        $this->validate(request(), [
            'name' => 'required',
            'url' => 'required',
            'type' => 'required',
            'pid' => 'required',
            'level' => 'required'
        ]);

        $check = Permission::where('name', request('name'))
            ->orWhere('url', request('url'))
            ->count();
        if ($check > 0) {
            throw new BaseResponseException('权限名称或权限重复', ResultCode::PARAMS_INVALID);
        }

        $permission = Permission::create(request()->all());

        return Result::success($permission);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function edit()
    {
        $this->validate(request(), [
            'id' => 'required|integer'
        ]);

        $id = request('id');
        $check = Permission::where('id', '<>', $id)
            ->where(function (Builder $query) {
                $query->where('name', request('name'))
                    ->orWhere('url', request('url'));
            })->count();
        if ($check > 0) {
            throw new BaseResponseException('权限名称或权限重复', ResultCode::PARAMS_INVALID);
        }

        DB::beginTransaction();
        try {
            $permission = Permission::where('id', $id)->update(request()->all());
            if ($permission) {
                RolePermission::where('permission_id', $id)->update([
                    'permission_name' => request('name'),
                    'permission_url' => request('url')
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new BaseResponseException('编辑失败', ResultCode::DB_UPDATE_FAIL);
        }
        DB::commit();

        return Result::success(['count' => $permission]);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete()
    {
        $this->validate(request(), [
            'id' => 'required|integer'
        ]);

        $id = request('id');

        Permission::destroy($id);

        return Result::success();
    }
}
