<?php

namespace App\Models;

use App\Http\Services\MpService;
use App\Scopes\DeleteScope;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Object_;

/**
 * @property array permissions 权限列表
 * @property int mp_id 自增id
 * @property mixed app_id
 * @property mixed app_secret
 */
class Mp extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $table = 'mps';

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'mp_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'api_token', 'app_id', 'app_secret', 'valid_token', 'valid_status', 'encodingaeskey'
    ];

    protected $attributes = [
        'api_token' => '', 'app_id' => '', 'app_secret' => '', 'valid_token' => '', 'valid_status' => self::VALID_FAIL, 'encodingaeskey' => ''
    ];

    const TEXT_MEDIA = 1;       //文本消息
    const NEWS_MEDIA = 2;       //单图文消息
    const MORE_NEWS_MEDIA = 3;  //多图文消息
    const IMAGE_MEDIA = 4;      //图片消息
    const VOICE_MEDIA = 5;      //语音消息
    const VIDEO_MEDIA = 6;      //视频消息
    const CARD_MEDIA = 7;       //卡券消息
    const MINI_APP_MEDIA = 8;   //小程序消息

    const VALID_SUCCESS = 1;    //对接成功
    const VALID_FAIL = 0;       //对接失败

    /**
     * 欢迎语
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function welcome()
    {
        return $this->hasOne('App\Models\MpWelcome', 'mp_id', 'mp_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Users', 'user_id', 'id');
    }

    /**
     * 权限
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permissions', 'mp_permissions', 'mp_id', 'permission_id', 'mp_id', 'id'
        );
    }
}
