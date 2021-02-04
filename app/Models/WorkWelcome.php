<?php

namespace App\Models;

use App\Scopes\DeleteScope;
use App\Scopes\WorkScope;
use Illuminate\Database\Eloquent\Model;

class WorkWelcome extends Model
{
    protected $table = 'work_welcomes';

    protected $primaryKey = 'w_wlcm_id';

    protected $fillable = [
        'work_id', 'w_user_id', 'text', 'image', 'link', 'miniprogram', 'welcome_type', 'name', 'status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'text' => 'object',
        'image' => 'object',
        'link' => 'object',
        'miniprogram' => 'object'
    ];

    const TEXT = 0;     //文本
    const IMAGE = 1;    //图片
    const LINK = 2;     //链接
    const MINI = 3;     //小程序

    /**
     * 归属成员
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\WorkUser', 'w_user_id', 'w_user_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new WorkScope());
        static::addGlobalScope(new DeleteScope());
    }
}
