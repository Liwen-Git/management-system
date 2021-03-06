<?php

namespace App\Http\Modules;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * @package App\Http\Modules
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property $email_verified_time
 * @property string $password
 * @property integer $role_id
 * @property integer $is_super
 *
 * @method static static find($id, $columns = ['*'])
 * @method static static where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static int count($columns = '*')
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return mixed
     * JWT use
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // 超级用户
    const SUPER_USER = 1;
}
