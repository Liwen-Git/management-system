<?php

namespace App\Http\Modules;

use Illuminate\Support\Carbon;

/**
 * Class Role
 * @package App\Http\Modules
 * @property string $name
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Role extends BaseModel
{
    protected $guarded = [];

    /**
     * 添加permission_id_arr属性到 ORM 模型中
     * @var array
     */
    protected $appends = ['permission_id_arr'];

    public function rolePermission()
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * 角色对应的权限id数组
     */
    public function getPermissionIdArrAttribute()
    {
        return $this->rolePermission()->pluck('permission_id')->all();
    }
}
