<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//用户登录
Route::group(['namespace' => 'System'], function () {
    Route::get('login', 'UsersController@loginForm')->name('users.loginForm');
    Route::post('login', 'UsersController@login')->name('users.login');
    Route::get('register', 'UsersController@registerForm')->name('users.registerForm');
    Route::post('register', 'UsersController@register')->name('users.register');
});

Route::group(['middleware' => ['auth', 'user.operate.log']], function () {
    //初始化
    Route::get('init', 'BaseController@initGet')->name('system.init');
    Route::get('clear', 'BaseController@clearGet')->name('system.clear');
    Route::get('home', 'BaseController@homeGet')->name('system.home');
    Route::post('upload/work/image', 'BaseController@uploadWorkImagePost')->name('system.upload.work.image');
    Route::post('upload/image', 'BaseController@uploadImagePost')->name('system.upload.image');


    Route::group(['namespace' => 'System'], function () {
        //退出登录
        Route::get('logout', 'UsersController@logout')->name('users.logout');
        //更改密码
        Route::get('change_password', 'UsersController@changePasswordForm')->name('users.changePasswordForm');
        Route::put('change_password', 'UsersController@changePasswordPut')->name('users.changePasswordPut');
    });

    /*
  |--------------------------------------------------------------------------
  | 公众号基础模块
  |--------------------------------------------------------------------------
  */
    Route::group(['namespace' => 'MpBase', 'prefix' => 'mp_base', 'middleware' => ['permission:pmsn.mp.base']], function () {
        //基础信息
        Route::group(['prefix' => 'info', 'middleware' => 'permission:pmsn.mp.base.info'], function () {
            Route::get('/', 'InfoController@indexGet')->name('mp.base.info');
            Route::post('/', 'InfoController@savePost')->name('mp.base.info.save');
            Route::post('command', 'InfoController@commandPost')->name('mp.base.info.command');
        });
        //关注欢迎语
        Route::group(['prefix' => 'welcomes', 'middleware' => ['permission:pmsn.mp.base.welcomes', 'user.mp.permission:pmsn.mp.base.welcomes']], function () {
            Route::get('/', 'WelcomesController@indexGet')->name('mp.base.welcomes');
            Route::put('/', 'WelcomesController@updatePut')->name('mp.base.welcomes.update');
        });

        //关键字回复
        Route::group(['prefix' => 'keywords', 'middleware' => ['permission:pmsn.mp.base.keywords', 'user.mp.permission:pmsn.mp.base.keywords']], function () {
            Route::get('/', 'KeywordsController@indexGet')->name('mp.base.keywords');
            Route::get('data', 'KeywordsController@dataGet')->name('mp.base.keywords.data');
            Route::get('create', 'KeywordsController@createGet')->name('mp.base.keywords.create');
            Route::post('store', 'KeywordsController@storePost')->name('mp.base.keywords.store');
            Route::get('{m_kw_id}/edit', 'KeywordsController@editGet')->name('mp.base.keywords.edit');
            Route::put('{m_kw_id}/update', 'KeywordsController@updatePut')->name('mp.base.keywords.update');
            Route::delete('destroy', 'KeywordsController@destroyDelete')->name('mp.base.keywords.destroy');
        });

        //自定义菜单
        Route::group(['prefix' => 'menus', 'middleware' => ['permission:pmsn.mp.base.menus', 'user.mp.permission:pmsn.mp.base.menus']], function () {
            Route::get('/', 'MenusController@indexGet')->name('mp.base.menus');
            Route::put('/', 'MenusController@updatePut')->name('mp.base.menus.update');
        });

        //粉丝管理
        Route::group(['prefix' => 'fans', 'middleware' => ['permission:pmsn.mp.base.fans', 'user.mp.permission:pmsn.mp.base.fans']], function () {
            Route::get('/', 'FansController@indexGet')->name('mp.base.fans');
            Route::get('data', 'FansController@dataGet')->name('mp.base.fans.data');
            Route::put('/{m_fan_id}/update', 'FansController@updatePut')->name('mp.base.fans.update');
            //粉丝标签(组)管理
            Route::get('tags', 'FansTagsController@indexGet')->name('mp.base.fans.tags');
            Route::get('tags/data', 'FansTagsController@dataGet')->name('mp.base.fans.tags.data');
            Route::get('tags/create', 'FansTagsController@createGet')->name('mp.base.fans.tags.create');
            Route::post('tags/store', 'FansTagsController@storePost')->name('mp.base.fans.tags.store');
            Route::get('tags/{m_tag_id}/edit', 'FansTagsController@editGet')->name('mp.base.fans.tags.edit');
            Route::put('tags/{m_tag_id}/update', 'FansTagsController@updatePut')->name('mp.base.fans.tags.update');
            Route::delete('tags/destroy', 'FansTagsController@destroyDelete')->name('mp.base.fans.tags.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | 公众号高级模块
    |--------------------------------------------------------------------------
    */
    Route::group(['namespace' => 'MpAdvanced', 'prefix' => 'mp_advanced', 'middleware' => ['permission:pmsn.mp.advanced']], function () {
        //模板消息
        Route::group(['prefix' => 'templates', 'middleware' => ['permission:pmsn.mp.advanced.templates', 'user.mp.permission:pmsn.mp.advanced.templates']], function () {
            Route::get('/', 'TemplatesController@indexGet')->name('mp.advanced.templates');
            Route::get('data', 'TemplatesController@dataGet')->name('mp.advanced.templates.data');
            Route::get('create', 'TemplatesController@createGet')->name('mp.advanced.templates.create');
            Route::post('store', 'TemplatesController@storePost')->name('mp.advanced.templates.store');
            Route::get('{m_temp_id}/edit', 'TemplatesController@editGet')->name('mp.advanced.templates.edit');
            Route::put('{m_temp_id}/update', 'TemplatesController@updatePut')->name('mp.advanced.templates.update');
            Route::delete('destroy', 'TemplatesController@destroyDelete')->name('mp.advanced.templates.destroy');
            Route::get('users', 'TemplatesController@usersGet')->name('mp.advanced.templates.users');
            Route::get('users/data', 'TemplatesController@usersDataGet')->name('mp.advanced.templates.users.data');
        });

        //渠道二维码
        Route::group(['prefix' => 'channel_codes', 'middleware' => ['permission:pmsn.mp.advanced.channel_codes', 'user.mp.permission:pmsn.mp.advanced.channel_codes']], function () {
            Route::get('/', 'ChannelCodesController@indexGet')->name('mp.advanced.channel_codes');
            Route::get('data', 'ChannelCodesController@dataGet')->name('mp.advanced.channel_codes.data');
            Route::get('record', 'ChannelCodesController@recordGet')->name('mp.advanced.channel_codes.record');
            Route::get('record/data', 'ChannelCodesController@recordDataGet')->name('mp.advanced.channel_codes.record.data');
            Route::get('create', 'ChannelCodesController@createGet')->name('mp.advanced.channel_codes.create');
            Route::post('store', 'ChannelCodesController@storePost')->name('mp.advanced.channel_codes.store');
            Route::get('{m_ccode_id}/edit', 'ChannelCodesController@editGet')->name('mp.advanced.channel_codes.edit');
            Route::put('{m_ccode_id}/update', 'ChannelCodesController@updatePut')->name('mp.advanced.channel_codes.update');
            Route::get('{m_ccode_id}/download', 'ChannelCodesController@downloadGet')->name('mp.advanced.channel_codes.download');
            Route::delete('destroy', 'ChannelCodesController@destroyDelete')->name('mp.advanced.channel_codes.destroy');
        });

        //长链接转短链接
        Route::group(['prefix' => 'short_urls', 'middleware' => ['permission:pmsn.mp.advanced.short_urls', 'user.mp.permission:pmsn.mp.advanced.short_urls']], function () {
            Route::get('/', 'ShortUrlsController@indexGet')->name('mp.advanced.short_urls');
            Route::get('data', 'ShortUrlsController@dataGet')->name('mp.advanced.short_urls.data');
            Route::get('create', 'ShortUrlsController@createGet')->name('mp.advanced.short_urls.create');
            Route::post('store', 'ShortUrlsController@storePost')->name('mp.advanced.short_urls.store');
            Route::get('{m_short_url_id}/edit', 'ShortUrlsController@editGet')->name('mp.advanced.short_urls.edit');
            Route::put('{m_short_url_id}/update', 'ShortUrlsController@updatePut')->name('mp.advanced.short_urls.update');
            Route::delete('destroy', 'ShortUrlsController@destroyDelete')->name('mp.advanced.short_urls.destroy');
        });


    });


    /*
    |--------------------------------------------------------------------------
    | 企业微信基础模块
    |--------------------------------------------------------------------------
    */
    Route::group(['namespace' => 'WorkBase', 'prefix' => 'work_base', 'middleware' => 'permission:pmsn.work.base'], function () {
        //基础信息
        Route::group(['prefix' => 'info', 'middleware' => 'permission:pmsn.work.base.info'], function () {
            Route::get('/', 'InfoController@indexGet')->name('work.base.info');
            Route::post('/', 'InfoController@savePost')->name('work.base.info.save');
            Route::post('command', 'InfoController@commandPost')->name('work.base.info.command');
        });
        //员工管理
        Route::group(['prefix' => 'users', 'middleware' => 'permission:pmsn.work.base.users'], function () {
            Route::get('/', 'UsersController@indexGet')->name('work.base.users');
            Route::get('data', 'UsersController@dataGet')->name('work.base.users.data');
        });
        //客户管理
        Route::group(['prefix' => 'customers', 'middleware' => 'permission:pmsn.work.base.customers'], function () {
            Route::get('/', 'CustomersController@indexGet')->name('work.base.customers');
            Route::get('data', 'CustomersController@dataGet')->name('work.base.customers.data');
            Route::put('tags', 'CustomersController@tagsPut')->name('work.base.customers.tags.update');

            //客户标签管理
            Route::group(['prefix' => 'tags'], function () {
                Route::get('/', 'CustomerTagsController@indexGet')->name('work.base.customer.tags');
                Route::put('/update', 'CustomerTagsController@updatePut')->name('work.base.customer.tags.update');
                Route::delete('/destroy', 'CustomerTagsController@destroyDelete')->name('work.base.customer.tags.delete');
            });
        });

        //客户群管理
        Route::group(['prefix' => 'group_chats', 'middleware' => 'permission:pmsn.work.base.group_chats'], function () {
            Route::get('/', 'GroupChatsController@indexGet')->name('work.base.group_chats');
            Route::get('data', 'GroupChatsController@dataGet')->name('work.base.group_chats.data');
            Route::get('customers', 'GroupChatsController@customersGet')->name('work.base.group_chats.customers');
            Route::get('customers/data', 'GroupChatsController@customersDataGet')->name('work.base.group_chats.customers.data');
        });
        //欢迎语
        Route::group(['prefix' => 'welcomes', 'middleware' => 'permission:pmsn.work.base.welcomes'], function () {
            Route::get('/', 'WelcomesController@indexGet')->name('work.base.welcomes');
            Route::get('data', 'WelcomesController@dataGet')->name('work.base.welcomes.data');
            //添加
            Route::get('create', 'WelcomesController@createGet')->name('work.base.welcomes.create');
            Route::post('store', 'WelcomesController@createPost')->name('work.base.welcomes.store');
            //编辑
            Route::get('{w_wlcm_id}/edit', 'WelcomesController@editGet')->name('work.base.welcomes.edit');
            Route::put('{w_wlcm_id}/update', 'WelcomesController@updatePut')->name('work.base.welcomes.update');
            //删除
            Route::delete('destroy', 'WelcomesController@destroyDelete')->name('work.base.welcomes.destroy');
        });
        //选择框搜索
        Route::group(['prefix' => 'select'], function () {
            Route::get('tags', 'SelectsController@TagsGet')->name('work.base.select.tags');
        });
    });

    /*
   |--------------------------------------------------------------------------
   | 企业微信高级模块
   |--------------------------------------------------------------------------
   */
    Route::group(['namespace' => 'WorkAdvanced', 'prefix' => 'work_advanced', 'middleware' => 'permission:pmsn.word.advanced'], function () {
        //渠道二维码
        Route::group(['prefix' => 'channel_codes', 'middleware' => 'permission:pmsn.work.advanced.channel_codes'], function () {
            Route::get('/', 'ChannelCodesController@indexGet')->name('work.advanced.channel_codes');
            Route::get('data', 'ChannelCodesController@dataGet')->name('work.advanced.channel_codes.data');
            //添加
            Route::get('create', 'ChannelCodesController@createGet')->name('work.advanced.channel_codes.create');
            Route::post('store', 'ChannelCodesController@createPost')->name('work.advanced.channel_codes.store');
            //编辑
            Route::get('{w_ccode_id}/edit', 'ChannelCodesController@editGet')->name('work.advanced.channel_codes.edit');
            Route::put('{w_ccode_id}/update', 'ChannelCodesController@updatePut')->name('work.advanced.channel_codes.update');
            //删除
            Route::delete('destroy', 'ChannelCodesController@destroyDelete')->name('work.advanced.channel_codes.destroy');

            //记录
            Route::get('{w_ccode_id}/record', 'ChannelCodesController@recordGet')->name('work.advanced.channel_codes.record');
            Route::get('{w_ccode_id}/record/data', 'ChannelCodesController@recordDataGet')->name('work.advanced.channel_codes.record.data');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | 系统管理模块
    |--------------------------------------------------------------------------
    */
    Route::group(['namespace' => 'System', 'prefix' => 'system', 'middleware' => 'permission:pmsn.system'], function () {
        //用户管理
        Route::group(['prefix' => 'users', 'middleware' => 'permission:pmsn.system.users'], function () {
            Route::get('/', 'UsersController@index')->name('system.users');
            Route::get('data', 'UsersController@data')->name('system.users.data');
            //添加
            Route::get('create', 'UsersController@create')->name('system.users.create');
            Route::post('store', 'UsersController@store')->name('system.users.store');
            //编辑
            Route::get('{id}/edit', 'UsersController@edit')->name('system.users.edit');
            Route::put('{id}/update', 'UsersController@update')->name('system.users.update');
            //删除
            Route::delete('destroy', 'UsersController@destroy')->name('system.users.destroy');
        });

        //角色管理
        Route::group(['prefix' => 'roles', 'middleware' => 'permission:pmsn.system.roles'], function () {
            Route::get('/', 'RolesController@index')->name('system.roles');
            Route::get('data', 'RolesController@data')->name('system.roles.data');
            //添加
            Route::get('create', 'RolesController@create')->name('system.roles.create');
            Route::post('store', 'RolesController@store')->name('system.roles.store');
            //编辑
            Route::get('{id}/edit', 'RolesController@edit')->name('system.roles.edit');
            Route::put('{id}/update', 'RolesController@update')->name('system.roles.update');
            //分配权限
            Route::get('{id}/permission', 'RolesController@permission')->name('system.roles.permission');
            Route::put('{id}/permission', 'RolesController@assignPermission')->name('system.roles.assignPermission');
            //删除
            Route::delete('destroy', 'RolesController@destroy')->name('system.roles.destroy');
        });

        //权限管理
        Route::group(['prefix' => 'permissions', 'middleware' => 'permission:pmsn.system.permissions'], function () {
            Route::get('/', 'PermissionsController@index')->name('system.permissions');
            Route::get('data', 'PermissionsController@data')->name('system.permissions.data');
            //添加
            Route::get('create', 'PermissionsController@create')->name('system.permissions.create');
            Route::post('store', 'PermissionsController@store')->name('system.permissions.store');
            //编辑
            Route::get('{id}/edit', 'PermissionsController@edit')->name('system.permissions.edit');
            Route::put('{id}/update', 'PermissionsController@update')->name('system.permissions.update');
            //删除
            Route::delete('destroy', 'PermissionsController@destroy')->name('system.permissions.destroy');
        });

        //配置管理
        Route::group(['prefix' => 'configs', 'middleware' => 'permission:pmsn.system.config'], function () {
            Route::get('/', 'ConfigController@index')->name('system.configs');
            Route::get('data', 'ConfigController@data')->name('system.configs.data');
            //添加
            Route::get('create', 'ConfigController@create')->name('system.configs.create');
            Route::post('store', 'ConfigController@store')->name('system.configs.store');
            //编辑
            Route::get('{id}/edit', 'ConfigController@edit')->name('system.configs.edit');
            Route::put('{id}/update', 'ConfigController@update')->name('system.configs.update');
            //删除
            Route::delete('destroy', 'ConfigController@destroy')->name('system.configs.destroy');
        });

        //日志管理
        Route::group(['prefix' => 'logs', 'middleware' => 'permission:pmsn.system.logs'], function () {
            Route::get('/', 'BaseController@logs')->name('system.logs');
            Route::get('data', 'BaseController@logsData')->name('system.logs.data');
        });
    });

    Route::get('/', function () {
        return view('base');
    });
});


