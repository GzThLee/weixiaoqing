<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //企业微信表
        Schema::create('works', function (Blueprint $table) {
            $table->increments('work_id')->comment('企业微信id');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->string('name', 64)->comment('企业微信名称');
            $table->string('corpid', 36)->comment('企业微信corpid');
            $table->string('corpsecret', 64)->comment('企业微信secret');
            $table->string('usersecret', 64)->comment('通信录secret');
            $table->string('token', 32)->comment('自定义token');
            $table->string('aes_key', 64)->comment('自定义EncodingAESKey');
            $table->string('api_token', 64)->comment('系统唯一对接token')->unique();
            $table->string('app_agentid', 64)->comment('自建应用agentid');
            $table->string('app_secret', 64)->comment('自建应用secret');
            $table->string('app_token', 64)->comment('自建应用token');
            $table->string('app_aes_key', 64)->comment('自建应用aes_key');
            $table->timestamps();
        });

        //企业微信欢迎语表
        Schema::create('work_welcomes', function (Blueprint $table) {
            $table->increments('w_wlcm_id')->comment('欢迎语id');
            $table->integer('work_id')->default(0)->comment('企业微信id');
            $table->integer('w_user_id')->default(0)->comment('成员id');
            $table->string('name', 64)->comment('欢迎语名称');
            $table->text('text')->comment('消息文本内容');
            $table->text('image')->comment('图片消息内容(json)');
            $table->text('link')->comment('图文消息内容(json)');
            $table->text('miniprogram')->comment('小程序消息内容(json)');
            $table->tinyInteger('welcome_type')->default(0)->comment('欢迎语类型');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //企业微信成员表
        Schema::create('work_users', function (Blueprint $table) {
            $table->increments('w_user_id')->comment('成员id');
            $table->integer('work_id')->default(0)->comment('企业微信id');
            $table->string('department_ids', 36)->comment('企业微信部门id列表(array)');
            $table->string('userid', 64)->comment('企业微信成员userid');
            $table->string('name', 64)->comment('企业微信成员名称');
            $table->string('open_userid', 64)->comment('企业微信全局唯一id');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //企业微信客户标签组表
        Schema::create('work_tag_groups', function (Blueprint $table) {
            $table->increments('w_tagg_id')->comment('标签组id');
            $table->integer('work_id')->default(0)->comment('企业微信id');
            $table->string('group_id', 128)->comment('企业微信标签组id');
            $table->string('name', 64)->comment('企业微信标签组名称');
            $table->integer('order')->default(0)->comment('企业微信标签组排序的次序值');
            $table->timestamps();
        });

        //企业微信客户标签表
        Schema::create('work_tags', function (Blueprint $table) {
            $table->increments('w_tag_id')->comment('标签id');
            $table->integer('w_tagg_id')->default(0)->comment('标签组id');
            $table->string('tag_id', 128)->comment('企业微信标签id');
            $table->string('name', 64)->comment('企业微信标签名称');
            $table->integer('order')->default(0)->comment('企业微信标签排序的次序值');
            $table->timestamps();
        });

        //企业微信微信(客户)群表
        Schema::create('work_groupchats', function (Blueprint $table) {
            $table->increments('w_gchat_id')->comment('群id');
            $table->integer('work_id')->default(0)->comment('企业微信id');
            $table->string('chat_id', 64)->comment('企业微信客户群id');
            $table->string('owner', 64)->comment('群主ID');
            $table->string('name', 64)->comment('群名');
            $table->text('notice')->comment('群公告');
            $table->timestamp('create_time')->nullable()->comment('群的创建时间');
            $table->tinyInteger('status')->default(0)->comment('企业微信客户群跟进状态');
            $table->timestamps();
        });

        //企业微信微信群客户表
        Schema::create('work_groupchat_customers', function (Blueprint $table) {
            $table->increments('w_gchat_cust_id')->comment('群客户记录id');
            $table->integer('w_gchat_id')->default(0)->comment('群id');
            $table->integer('w_cust_id')->default(0)->comment('客户id');
            $table->string('userid', 64)->comment('企业微信成员userid');
            $table->timestamp('join_time')->comment('入群时间');
            $table->tinyInteger('join_scene')->default(0)->comment('入群方式');
            $table->tinyInteger('type')->default(0)->comment('成员类型');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //企业微信部门表
        Schema::create('work_departments', function (Blueprint $table) {
            $table->increments('w_dept_id')->comment('部门id');
            $table->integer('department_id')->default(0)->comment('企业微信部门id');
            $table->integer('work_id')->default(0)->comment('企业微信id');
            $table->string('name', 64)->comment('部门名称');
            $table->string('name_en', 64)->comment('部门英文名称');
            $table->integer('parentid')->default(0)->comment('企业微信父部门id');
            $table->integer('order')->default(0)->comment('部门次序值');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //企业微信客户表
        Schema::create('work_customers', function (Blueprint $table) {
            $table->increments('w_cust_id')->comment('客户id');
            $table->integer('work_id')->default(0)->comment('企业微信id');
            $table->string('external_userid', 128)->comment('客户userid');
            $table->string('name', 64)->comment('客户名称');
            $table->string('unionid', 64)->comment('客户微信unionid');
            $table->tinyInteger('type')->default(0)->comment('客户类型');
            $table->string('avatar', 256)->comment('客户头像');
            $table->tinyInteger('gender')->default(0)->comment('客户性别');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //企业微信客户跟随成员表
        Schema::create('work_customers_follow', function (Blueprint $table) {
            $table->increments('w_cust_follow_id')->comment('客户跟随记录id');
            $table->integer('w_cust_id')->default(0)->comment('客户id');
            $table->integer('w_user_id')->default(0)->comment('成员id');
            $table->string('userid', 64)->comment('企业微信成员userid');
            $table->string('remark', 36)->comment('备注');
            $table->timestamp('create_time')->nullable()->comment('添加时间');
            $table->string('state', 128)->comment('企业微信自定义的state参数');
            $table->string('description', 256)->comment('描述');
            $table->text('remark_mobiles')->comment('备注的手机号码(array)');
            $table->string('add_way', 16)->comment('添加此客户的来源');
            $table->text('tags')->comment('打标签集合(array)');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //企业微信客户标签中间表
        Schema::create('work_customer_tags', function (Blueprint $table) {
            $table->integer('w_cust_id')->default(0)->comment('客户id');
            $table->integer('w_tag_id')->default(0)->comment('标签id');
        });

        //企业微信渠道二维码表
        Schema::create('work_channel_codes', function (Blueprint $table) {
            $table->increments('w_ccode_id')->comment('渠道二维码id');
            $table->integer('work_id')->default(0)->comment('企业微信id');
            $table->integer('w_user_id')->default(0)->comment('成员id');
            $table->string('name', 64)->comment('名称');
            $table->text('text')->comment('消息文本内容');
            $table->text('image')->comment('图片消息内容(json)');
            $table->text('link')->comment('图文消息内容(json)');
            $table->text('miniprogram')->comment('小程序消息内容(json)');
            $table->tinyInteger('welcome_type')->default(0)->comment('欢迎语类型');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->string('config_id', 64)->comment('新增联系方式的配置id');
            $table->string('qr_code_url', 256)->comment('生成二维码文件地址');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //企业微信渠道二维码客户表
        Schema::create('work_channel_code_customers', function (Blueprint $table) {
            $table->increments('w_ccode_cust_id')->comment('渠道二维码记录id');;
            $table->integer('w_ccode_id')->default(0)->comment('渠道二维码id');
            $table->integer('w_cust_id')->default(0)->comment('客户id');
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
        Schema::dropIfExists('works');
        Schema::dropIfExists('work_welcomes');
        Schema::dropIfExists('work_users');
        Schema::dropIfExists('work_tag_groups');
        Schema::dropIfExists('work_tags');
        Schema::dropIfExists('work_groupchats');
        Schema::dropIfExists('work_groupchat_customers');
        Schema::dropIfExists('work_departments');
        Schema::dropIfExists('work_customers');
        Schema::dropIfExists('work_customers_follow');
        Schema::dropIfExists('work_customer_tags');
        Schema::dropIfExists('work_channel_codes');
        Schema::dropIfExists('work_channel_code_customers');
    }
}
