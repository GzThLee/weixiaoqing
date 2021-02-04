<?php

namespace App\Models;

use App\Scopes\SuccessScope;
use App\Scopes\WorkScope;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed w_user_id
 */
class WorkUser extends Model
{
    protected $table = 'work_users';

    protected $primaryKey = 'w_user_id';

    protected $fillable = [
        'work_id', 'userid', 'department_ids', 'name', 'open_userid', 'status'
    ];

    /**
     * 这个属性应该被转换为原生类型.
     *
     * @var array
     */
    protected $casts = ['department_ids' => 'array'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new WorkScope());
        static::addGlobalScope(new SuccessScope());
    }
}
