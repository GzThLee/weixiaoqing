<?php

namespace App\Models;

use App\Scopes\SuccessScope;
use App\Scopes\WorkScope;
use Illuminate\Database\Eloquent\Model;

class WorkCustomer extends Model
{
    protected $table = 'work_customers';

    protected $primaryKey = 'w_cust_id';

    protected $fillable = [
        'w_user_id', 'work_id', 'external_userid', 'name', 'unionid', 'type', 'avatar', 'gender', 'status'
    ];

    /**
     * 添加联系人记录
     */
    public function follows()
    {
        return $this->hasMany('App\Models\WorkCustomersFollow', 'w_cust_id', 'w_cust_id');
    }

    /**
     * 标签
     */
    public function tags()
    {
        return $this->belongsToMany('App\Models\WorkTag', 'work_customer_tags', 'w_cust_id', 'w_tag_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new WorkScope());
    }
}
