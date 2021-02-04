<?php

namespace App\Models;

use App\Scopes\DeleteScope;
use App\Scopes\WorkScope;
use Illuminate\Database\Eloquent\Model;

class WorkChannelCode extends Model
{
    protected $table = 'work_channel_codes';

    protected $primaryKey = 'w_ccode_id';

    protected $fillable = [
        'work_id', 'w_user_id', 'name', 'text', 'image', 'link', 'miniprogram', 'welcome_type', 'end_time', 'status', 'config_id', 'qr_code_url'
    ];

    protected $casts = [
        'text' => 'object',
        'image' => 'object',
        'link' => 'object',
        'miniprogram' => 'object'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\WorkUser', 'w_user_id', 'w_user_id');
    }

    public function record()
    {
        return $this->hasMany('App\Models\WorkChannelCodeCustomer', 'w_ccode_id', 'w_ccode_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new WorkScope());
        static::addGlobalScope(new DeleteScope());
    }
}
