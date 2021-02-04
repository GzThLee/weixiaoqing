<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //微信公众号表
        Schema::create('mps', function (Blueprint $table) {
            $table->increments('mp_id')->comment('公众号ID');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->string('api_token',50)->comment('系统唯一对接token')->unique();
            $table->string('name',128)->comment('名称');
            $table->string('app_id',30)->comment('公众号appid');
            $table->string('app_secret',32)->comment('公众号appsecret');
            $table->string('valid_token',40);
            $table->tinyInteger('valid_status')->default(0)->comment('接入状态');
            $table->string('encodingaeskey',50)->comment('自定义EncodingAESKey');
            $table->timestamps();
        });

        //公众号权限中间表
        Schema::create('mp_permissions', function (Blueprint $table) {
            $table->integer('mp_id')->default(0)->comment('公众号id');
            $table->bigInteger('permission_id')->default(0)->comment('权限id');
        });

        //关注欢迎语表
        Schema::create('mp_welcomes', function (Blueprint $table) {
            $table->increments('mp_id')->comment('公众号id');
            $table->text('content')->comment('回复内容(json)');
            $table->tinyInteger('media_type')->default(0)->comment('回复消息类型');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //模板消息表
        Schema::create('mp_templates', function (Blueprint $table) {
            $table->increments('m_temp_id')->comment('模板消息id');
            $table->integer('mp_id')->default(0)->comment('公众号id');
            $table->string('theme')->comment('主题名称');
            $table->integer('push_obj')->default(0)->comment('推送对象(分组标签id)');
            $table->string('template_id',50)->comment('模板id');
            $table->timestamp('push_time')->nullable()->comment('推送时间');
            $table->text('content')->comment('回复内容(json)');
            $table->timestamp('finish_time')->nullable()->comment('完成时间');
            $table->integer('send_count')->default(0)->comment('发送人数');
            $table->integer('send_success_count')->default(0)->comment('成功人数');
            $table->integer('send_fail_count')->default(0)->comment('失败人数');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //长链接转短链接表
        Schema::create('mp_short_urls', function (Blueprint $table) {
            $table->increments('m_short_url_id')->comment('长转短链接id');
            $table->integer('mp_id')->default(0)->comment('公众号id');
            $table->string('name',128)->comment('名称');
            $table->string('long_url',512)->comment('长链接');
            $table->string('short_url',128)->comment('短链接');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //自定义菜单表
        Schema::create('mp_menus', function (Blueprint $table) {
            $table->increments('m_menu_id')->comment('菜单id');
            $table->integer('mp_id')->default(0)->comment('公众号id');
            $table->tinyInteger('menu_type')->default(0)->comment('菜单类型');
            $table->integer('pindex')->default(0)->comment('父id');
            $table->integer('index')->default(0)->comment('菜单位置');
            $table->integer('sort')->default(0)->comment('排序');
            $table->string('name',128)->comment('菜单名称');
            $table->text('content')->comment('菜单内容(json)');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //关键词回复表
        Schema::create('mp_keywords', function (Blueprint $table) {
            $table->increments('m_kw_id')->comment('关键字回复id');
            $table->integer('mp_id')->default(0)->comment('公众号id');
            $table->string('keyword',50)->comment('关键字');
            $table->integer('m_tag_id')->default(0)->comment('分组标签id');
            $table->tinyInteger('rule_type')->default(0)->comment('匹配规则');
            $table->text('content')->comment('回复内容(json)');
            $table->tinyInteger('media_type')->default(0)->comment('回复消息类型');
            $table->integer('trigger_count')->default(0)->comment('触发次数');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //关键词回复记录表
        Schema::create('mp_keyword_records', function (Blueprint $table) {
            $table->increments('m_kwr_id')->comment('关键字回复记录id');
            $table->integer('m_kw_id')->default(0)->comment('关键字回复id');
            $table->integer('m_fan_id')->default(0)->comment('粉丝id');
            $table->timestamps();
        });

        //粉丝标签表
        Schema::create('mp_tags', function (Blueprint $table) {
            $table->increments('m_tag_id')->comment('分组标签id');
            $table->integer('mp_id')->default(0)->comment('公众号id');
            $table->string('name',128)->comment('名称');
            $table->text('description')->comment('描述');
            $table->integer('tag_id')->default(0)->comment('公众号粉丝标签id');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //粉丝标签关联表
        Schema::create('mp_fans_tags', function (Blueprint $table) {
            $table->integer('m_fan_id')->default(0)->comment('粉丝id');
            $table->integer('m_tag_id')->default(0)->comment('分组标签id');
            $table->unique(['m_fan_id','m_tag_id'],'fans_tags_index');
        });

        //粉丝表
        Schema::create('mp_fans', function (Blueprint $table) {
            $table->increments('m_fan_id')->comment('粉丝id');
            $table->integer('mp_id')->default(0)->comment('公众号id');
            $table->string('nickname',64)->comment('昵称');
            $table->string('openid',30)->comment('openid');
            $table->string('remark',50)->comment('备注');
            $table->string('city',50)->comment('城市');
            $table->string('province',50)->comment('省份');
            $table->string('country',50)->comment('国家');
            $table->string('language',50)->comment('使用语言');
            $table->tinyInteger('sex')->default(0)->comment('性别');
            $table->string('headimgurl')->comment('头像');
            $table->tinyInteger('is_subscribe')->default(0)->comment('是否关注');
            $table->timestamp('subscribe_time')->nullable()->comment('关注时间');
            $table->timestamp('unsubscribe_time')->nullable()->comment('取消关注时间');
            $table->timestamp('last_time')->nullable()->comment('最后操作时间');
            $table->string('unionid',50)->comment('unionid');
            $table->tinyInteger('subscribe_type')->default(0)->comment('关注类型');
            $table->string('subscribe_scene',191)->comment('关注场景');
            $table->string('qr_scene',191)->comment('二维码场景');
            $table->string('qr_scene_str',191)->comment('二维码场景值');
            $table->timestamps();
        });

        //渠道二维码表
        Schema::create('mp_channel_codes', function (Blueprint $table) {
            $table->increments('m_ccode_id')->comment('渠道二维码id');
            $table->integer('mp_id')->default(0)->comment('公众号id');
            $table->string('ticket',150)->comment('二维码Ticket');
            $table->string('scene_str',64)->comment('场景值');
            $table->string('name',128)->comment('名称');
            $table->integer('m_tag_id')->default(0)->comment('分组标签id');
            $table->string('url')->comment('二维码图片解析后的地址');
            $table->text('ticket_url')->comment('ticket换取二维码地址');
            $table->integer('scan_count')->default(0)->comment('扫码次数');
            $table->integer('new_fans_subscribe')->default(0)->comment('新增粉丝关注数');
            $table->integer('new_fans_unsubscribe')->default(0)->comment('新增粉丝取关数');
            $table->integer('old_fans_subscribe')->default(0)->comment('旧粉丝数关注数');
            $table->integer('old_fans_unsubscribe')->default(0)->comment('旧粉丝数取关数');
            $table->text('content')->comment('回复内容(json)');
            $table->tinyInteger('media_type')->default(0)->comment('回复消息类型');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //渠道二维码粉丝记录表
        Schema::create('mp_channel_code_fans', function (Blueprint $table) {
            $table->increments('m_ccode_fan_id')->comment('渠道二维码记录id');
            $table->integer('m_ccode_id')->default(0)->comment('渠道二维码id');
            $table->integer('m_fan_id')->default(0)->comment('粉丝id');
            $table->tinyInteger('scan_type')->default(0)->comment('扫码关注类型');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mps');
        Schema::dropIfExists('mp_permissions');
        Schema::dropIfExists('mp_welcomes');
        Schema::dropIfExists('mp_templates');
        Schema::dropIfExists('mp_short_urls');
        Schema::dropIfExists('mp_menus');
        Schema::dropIfExists('mp_keywords');
        Schema::dropIfExists('mp_keyword_records');
        Schema::dropIfExists('mp_tags');
        Schema::dropIfExists('mp_fans_tags');
        Schema::dropIfExists('mp_fans');
        Schema::dropIfExists('mp_channel_codes');
        Schema::dropIfExists('mp_channel_code_fans');
    }
}
