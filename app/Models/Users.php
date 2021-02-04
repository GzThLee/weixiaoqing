<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

/**
 * @property Work work 账号所属企业微信
 * @property Mp mp 账号所属公众号
 * @property string nickname 用户昵称
 */
class Users extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'username', 'email', 'nickname', 'mobile', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'email_verified_at'
    ];

    protected $casts = [
        'email_verified_at' => 'date'
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * 所属企业微信
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function work()
    {
        return $this->hasOne('App\Models\Work', 'user_id', 'id');
    }

    /**
     * 所属微信公众号
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mp()
    {
        return $this->hasOne('App\Models\Mp', 'user_id', 'id');
    }
}
