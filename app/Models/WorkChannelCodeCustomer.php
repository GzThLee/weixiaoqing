<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkChannelCodeCustomer extends Model
{
    protected $table = 'work_channel_code_customers';

    protected $primaryKey = 'w_ccode_cust_id';

    protected $fillable = [
        'w_ccode_id', 'w_cust_id'
    ];

    /**
     * 客户资料
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function customer()
    {
        return $this->hasOne('App\Models\WorkCustomer', 'w_cust_id', 'w_cust_id');
    }
}
