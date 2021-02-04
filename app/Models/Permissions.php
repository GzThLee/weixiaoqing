<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;

/**
 * @property int type 类型
 */
class Permissions extends Permission
{

    protected $fillable = [
        "pid", "name", "guard_name", "display_name", "route_name", "icon", "sort", "type"
    ];

    const BUTTON_TYPE = 1;  //按钮
    const MENU_TYPE = 2;    //菜单

    protected $appends = ['type_name'];

    public function getTypeNameAttribute()
    {
        return $this->attributes['type_name'] = Arr::get([$this::BUTTON_TYPE => '按钮', $this::MENU_TYPE => '菜单'], $this->type);
    }

    //子权限
    public function childs()
    {
        return $this->hasMany('App\Models\Permissions', 'pid', 'id')->orderByDesc('sort');
    }

    //所有子权限递归
    public function allChilds()
    {
        return $this->childs()->with('allChilds');
    }
}
