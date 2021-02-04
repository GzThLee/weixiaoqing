<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkGroupchatCustomer extends Model
{
    protected $table = 'work_groupchat_customers';

    protected $primaryKey = 'w_gchat_cust_id';

    protected $fillable = [
        'w_gchat_id', 'w_cust_id', 'userid', 'join_time', 'join_scene', 'type'
    ];

    /**
     * 客户信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function customer()
    {
        return $this->hasOne('App\Models\WorkCustomer', 'w_cust_id', 'w_cust_id');
    }

    /**
     * 成员信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\Models\WorkUser', 'userid', 'userid');
    }
}
