网站地址：[http://www.weixiaoqing.com](http://www.weixiaoqing.com)

系统地址：[http://wechat.weixiaoqing.com](http://wechat.weixiaoqing.com)

进入系统立即注册使用

## 微小擎
微小擎，PHP免费开源的微信管理系统，基于laravel6.0内核与layui前端框架，便于开发者二次开发。系统实现了**微信公众号**、的大部分功能如：菜单设置，粉丝管理，关键字回复，渠道二维码，模板消息等。**企业微信**功能如：客户管理，成员管理，自定义欢迎语，渠道二维码等。我致力于长期优化升级，系统提供永久免费使用。

## 功能列表
- [x] 公众号基础功能 
    - [x] 公众号配置 
    - [x] 关注欢迎语 
    - [x] 自定义菜单 
    - [x] 关键字回复 
    - [x] 粉丝管理 
- [x] 公众号高级功能 
    - [x] 模板消息 
    - [x] 渠道二维码 
    - [ ] 活跃用户推送 
    - [ ] 关注定时推送 
    - [ ] 海报二维码 
    - [x] 长链接转短链接 
- [x] 企业微信基础功能 
    - [x] 企业微信配置 
    - [x] 成员管理 
    - [x] 加好友欢迎语
    - [x] 客户管理
    - [x] 客户标签管理
    - [x] 客户群管理
- [x] 企业微信高级功能 
    - [x] 渠道二维码 
    - [ ] 好友任务
    - [ ] 进群任务
- [x] 系统管理 
    - [x] 用户管理 
    - [x] 角色管理 
    - [x] 权限管理 
    - [x] 配置管理 
    - [x] 日志管理

## 安装
- LNMP环境安装（linux+nginx+mysql+php7.2）,PS:不会自行百度
- 复制.env.example 为.env
```
cp .env.example .env
```
- 新建数据库，字符集：utf8mb4，排序规则：utf8mb4_unicode_ci
- 配置.env里的数据库连接信息
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```
- 项目依赖安装:```composer install```
- 生成项目秘钥:```php artisan key:generate```
- 初始化数据表:```php artisan migrate```
- 创建默认数据:```php artisan db:seed```
- 创建符号链接:```php artisan storage:link```
- 打开定时器:```crontab -e```
- 添加调度器:```* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1```
- 安装配置supervisor队列监听
    - centos:```yum install supervisor```
    - ubuntu:```sudo apt-get install supervisor```
    - 编辑supervisor配置:```vim /etc/supervisord.conf```
    - 最后一行改成:```files = supervisord.d/*.conf```
    - 复制项目配置到supervisor配置:```cp /path-to-your-project/weixiaoqing-work.conf /etc/supervisord.d/weixiaoqing-work.conf```
    - 启动supervisor服务:```supervisord -c /etc/supervisord.conf```
    - 启动supervisor服务配置:```supervisorctl start all```
- 登录系统：host，帐号：root  密码：123456

## 特别鸣谢
- **[⭐️腾讯云服务商⭐️](https://partner.cloud.tencent.com/invitation/2149759630592423e481692/100017935150)**
- **[Laravel 6.0](https://laravel.com/)**
- **[EasyWeChat 微信开发包](https://www.easywechat.com/)**
- **[Layui](https://www.layui.com/)**
- **[Layuimini](http://layuimini.99php.cn/)**
- [intervention](http://image.intervention.io/)
- [laravel-permission](https://github.com/spatie/laravel-permission)
- [curl](https://github.com/ixudra/Curl)
- [captcha](https://github.com/mewebstudio/captcha)
- [qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode)
- [Laravel 项目开发规范](https://learnku.com/docs/laravel-specification/7.x)
- [PHP PSR标准](https://www.php-fig.org/psr/)

## 疑问&帮助
- 如果你觉得项目不错，希望你可以给我的项目点个⭐️
- 如需协助加我QQ：361755806
- 腾讯云服务问题，可点击联系【 [殿堂级服务商](https://partner.cloud.tencent.com/invitation/2149759630592423e481692/100017935150) 】排忧解难
- 🙇🏻谢谢您
