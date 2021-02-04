<?php

namespace App\Models;

use App\Scopes\SuccessScope;
use App\Scopes\WorkScope;
use Illuminate\Database\Eloquent\Model;

class WorkDepartment extends Model
{
    protected $table = 'work_departments';

    protected $primaryKey = 'w_dept_id';

    protected $fillable = [
        'department_id', 'work_id', 'name', 'name_en', 'parentid', 'order', 'status'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new WorkScope());
        static::addGlobalScope(new SuccessScope());
    }
}
