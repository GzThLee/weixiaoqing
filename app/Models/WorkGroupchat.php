<?php

namespace App\Models;

use App\Scopes\SuccessScope;
use App\Scopes\WorkScope;
use Illuminate\Database\Eloquent\Model;

class WorkGroupchat extends Model
{
    protected $table = 'work_groupchats';

    protected $primaryKey = 'w_gchat_id';

    protected $fillable = [
        'work_id', 'chat_id', 'owner', 'name', 'create_time', 'status', 'notice'
    ];

    /**
     * 群主
     */
    public function owner()
    {
        return $this->hasOne('App\Models\WorkUser', 'userid', 'owner');
    }

    /**
     * 客户列表
     * @return \Illuminate\Database\Eloquent\Relations\hasManyThrough
     */
    public function customers()
    {
        return $this->hasManyThrough('App\Models\WorkCustomer', 'App\Models\WorkGroupchatCustomer', 'w_gchat_id', 'w_cust_id', 'w_gchat_id', 'w_cust_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new WorkScope());
    }
}
