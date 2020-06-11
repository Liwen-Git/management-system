<?php

namespace App\Http\Controllers\Api;


use App\Http\Modules\Role;
use App\Result;

class RoleController extends Controller
{
    public function list()
    {
        $list = Role::all();

        return Result::success($list);
    }
}
