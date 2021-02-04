<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer work_id 主键
 */
class Work extends Model
{
    protected $table = 'works';

    protected $primaryKey = 'work_id';

    protected $fillable = [
        'user_id', 'name', 'corpid', 'corpsecret', 'usersecret', 'api_token', 'token', 'aes_key', 'app_agentid', 'app_secret', 'app_token', 'app_aes_key'
    ];

    protected $attributes = [
        'corpid' => '', 'corpsecret' => '', 'usersecret' => '', 'api_token' => '', 'token' => '', 'aes_key' => '', 'app_agentid' => '', 'app_secret' => '', 'app_token' => '', 'app_aes_key' => ''
    ];
}
