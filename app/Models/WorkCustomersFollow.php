<?php

namespace App\Models;

use App\Scopes\SuccessScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class WorkCustomersFollow extends Model
{
    protected $table = 'work_customers_follow';

    protected $primaryKey = 'w_cust_follow_id';

    protected $fillable = [
        'w_cust_id', 'w_user_id', 'userid', 'remark', 'create_time', 'state', 'description', 'remark_mobiles', 'add_way', 'tags', 'status'
    ];

    public function setCreateTimeAttribute($value)
    {
        $this->attributes['create_time'] = date('Y-m-d H:i:s', $value);
    }

    /**
     *  模型的默认属性值。
     *
     * @var array
     */
    protected $attributes = [
        'state' => '',
    ];

    /**
     * 这个属性应该被转换为原生类型.
     *
     * @var array
     */
    protected $casts = ['remark_mobiles' => 'array', 'tags' => 'array'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SuccessScope());
    }

    public function user()
    {
        return $this->belongsTo('App\Models\WorkUser', 'w_user_id', 'w_user_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\WorkCustomer', 'w_cust_id', 'w_cust_id');
    }
}
