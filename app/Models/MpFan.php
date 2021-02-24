<?php

namespace App\Models;

use App\Scopes\MpScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class MpFan extends Model
{
    protected $table = 'mp_fans';

    protected $primaryKey = 'm_fan_id';

    protected $fillable = [
        'nickname', 'openid', 'remark', 'city', 'province', 'country', 'language', 'sex', 'headimgurl', 'mobile', 'is_subscribe', 'subscribe_time', 'unsubscribe_time', 'last_time', 'unionid', 'mp_id', 'subscribe_type', 'subscribe_scene', 'qr_scene', 'qr_scene_str'
    ];

    protected $attributes = [
        'unionid' => '',
        'nickname' => '',
        'remark' => '',
        'city' => '',
        'province' => '',
        'country' => '',
        'language' => '',
        'sex' => 0,
        'headimgurl' => '',
        'subscribe_type' => 0,
        'subscribe_scene' => '',
        'qr_scene' => '',
        'qr_scene_str' => '',
    ];

    protected $appends = ['subscribe_scene_text', 'sex_text'];

    const SCAN_UNDEFINED = 0;   //未知扫码关注（普通扫码）
    const SCAN_SUBSCRIBE = 1;   //扫码关注
    const SCAN_SUBSCRIBED = 2;   //已关注再扫码
    const SCAN_SUBSCRIBE_UNSUBSCRIBE = 3;   //扫码关注取消关注
    const SCAN_SUBSCRIBED_UNSUBSCRIBE = 4;   //已关注取消关注

    const SUBSCRIBE = 1;
    const UNSUBSCRIBE = 0;

    public function setSubscribeTimeAttribute($value)
    {
        $this->attributes['subscribe_time'] = is_numeric($value) ? date('Y-m-d H:i:s', $value) : $value;
    }

    public static function getSubscribeSceneText($subscribe_scene)
    {
        return Arr::get(['ADD_SCENE_SEARCH' => '公众号搜索', 'ADD_SCENE_ACCOUNT_MIGRATION' => '公众号迁移', 'ADD_SCENE_PROFILE_CARD' => '名片分享', 'ADD_SCENE_QR_CODE' => '扫描二维码', 'ADD_SCENE_PROFILE_LINK' => '图文页内名称点击', 'ADD_SCENE_PROFILE_ITEM' => '图文页右上角菜单', 'ADD_SCENE_PAID' => '支付后关注', 'ADD_SCENE_WECHAT_ADVERTISEMENT' => '微信广告', 'ADD_SCENE_OTHERS' => '其他'], $subscribe_scene) ?? '普通关注';
    }

    public function getSubscribeSceneTextAttribute()
    {
        return $this->attributes['subscribe_scene_text'] = MpFan::getSubscribeSceneText($this->subscribe_scene);
    }

    public function getSexTextAttribute()
    {
        return $this->attributes['sex_text'] = Arr::get([1 => '男', 2 => '女', 0 => '未知'], $this->sex) ?? '未知';
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new MpScope());
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\MpTag', 'mp_fans_tags', 'm_fan_id', 'm_tag_id');
    }

    public function mp()
    {
        return $this->belongsTo('App\Models\Mp', 'mp_id', 'mp_id');
    }

    public function channelCode()
    {
        return $this->belongsToMany('App\Models\MpChannelCode', 'mp_channel_code_fans', 'm_fan_id', 'm_ccode_id')->withTimestamps();
    }
}
