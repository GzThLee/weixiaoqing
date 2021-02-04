<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ system_config('site_title') }}-注册账号</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="{{ asset('layuimini/lib/layui-v2.5.5/css/layui.css') }}" media="all">
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .main-body {
            top: 50%;
            left: 50%;
            position: absolute;
            -webkit-transform: translate(-50%, -50%);
            -moz-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            -o-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            overflow: hidden;
        }

        .login-main .login-bottom .center .item input {
            display: inline-block;
            width: 227px;
            height: 22px;
            padding: 0;
            position: absolute;
            border: 0;
            outline: 0;
            font-size: 14px;
            letter-spacing: 0;
        }

        .login-main .login-bottom .center .item .icon-1 {
            background: url({{ asset('layuimini/images/icon-login.png') }}) no-repeat 1px 0;
        }

        .login-main .login-bottom .center .item .icon-2 {
            background: url({{ asset('layuimini/images/icon-login.png') }}) no-repeat -54px 0;
        }

        .login-main .login-bottom .center .item .icon-3 {
            background: url({{ asset('layuimini/images/icon-login.png') }}) no-repeat -106px 0;
        }

        .login-main .login-bottom .center .item .icon-4 {
            background: url({{ asset('layuimini/images/icon-login.png') }}) no-repeat 0 -43px;
            position: absolute;
            right: -10px;
            cursor: pointer;
        }

        .login-main .login-bottom .center .item .icon {
            display: inline-block;
            width: 33px;
            height: 22px;
        }

        .login-main .login-bottom .center .item {
            width: 288px;
            height: 35px;
            border-bottom: 1px solid #dae1e6;
            margin-bottom: 15px;
        }

        .login-main {
            width: 428px;
            position: relative;
            float: left;
        }

        .login-main .login-top {
            height: 117px;
            background-color: #148be4;
            border-radius: 12px 12px 0 0;
            font-family: SourceHanSansCN-Regular;
            font-size: 30px;
            font-weight: 400;
            font-stretch: normal;
            letter-spacing: 0;
            color: #fff;
            line-height: 117px;
            text-align: center;
            overflow: hidden;
            -webkit-transform: rotate(0);
            -moz-transform: rotate(0);
            -ms-transform: rotate(0);
            -o-transform: rotate(0);
            transform: rotate(0);
        }

        .login-main .login-top .bg1 {
            display: inline-block;
            width: 74px;
            height: 74px;
            background: #fff;
            opacity: .1;
            border-radius: 0 74px 0 0;
            position: absolute;
            left: 0;
            top: 43px;
        }

        .login-main .login-top .bg2 {
            display: inline-block;
            width: 94px;
            height: 94px;
            background: #fff;
            opacity: .1;
            border-radius: 50%;
            position: absolute;
            right: -16px;
            top: -16px;
        }

        .login-main .login-bottom {
            width: 428px;
            background: #fff;
            border-radius: 0 0 12px 12px;
            padding-bottom: 53px;
        }

        .login-main .login-bottom .center {
            width: 288px;
            margin: 0 auto;
            padding-top: 40px;
            position: relative;
        }

        body {
            background: url({{ asset('layuimini/images/loginbg.png') }}) 0% 0% / cover no-repeat;
            position: static;
            font-size: 12px;
        }

        input::-webkit-input-placeholder {
            color: #a6aebf;
        }

        input::-moz-placeholder { /* Mozilla Firefox 19+ */
            color: #a6aebf;
        }

        input:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
            color: #a6aebf;
        }

        input:-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: #a6aebf;
        }

        input:-webkit-autofill { /* 取消Chrome记住密码的背景颜色 */
            -webkit-box-shadow: 0 0 0 1000px white inset !important;
        }

        html {
            height: 100%;
        }

        .layui-form-item {
            text-align: center;
            width: 100%;
            height: 100%;
            margin: 0;
        }

        .login-main .login-bottom .register-btn {
            width: 288px;
            height: 40px;
            background-color: #1E9FFF;
            border-radius: 16px;
            margin: 12px auto 0;
            text-align: center;
            line-height: 40px;
            color: #fff;
            font-size: 14px;
            letter-spacing: 0;
            cursor: pointer;
            border: none;
        }

        .login-main .login-bottom .center .item .validateImg {
            position: absolute;
            right: 1px;
            bottom: 1px;
            cursor: pointer;
            height: 36px;
            border: 1px solid #e6e6e6;
        }

        @media screen and (max-width: 428px) {
            .login-main {
                width: 360px !important;
            }

            .login-main .login-top {
                width: 360px !important;
            }

            .login-main .login-bottom {
                width: 360px !important;
            }
        }
    </style>
</head>
<body>
<div class="main-body">
    <div class="login-main">
        <div class="login-top">
            <span>注册账号</span>
            <span class="bg1"></span>
            <span class="bg2"></span>
        </div>
        <form action="{{ route('users.register') }}" method="post" class="layui-form login-bottom">
            {{csrf_field()}}
            <div class="center">
                <div class="item">
                    <span class="icon icon-2"></span>
                    <input type="text" name="nickname" lay-verify="required" placeholder="请输入账号昵称" maxlength="24" value="{{ old('nickname') }}"/>
                </div>

                <div class="item">
                    <span class="icon icon-2"></span>
                    <input type="text" name="username" lay-verify="required" placeholder="请输入登录账号" maxlength="24" value="{{ old('username') }}"/>
                </div>

                <div class="item">
                    <span class="icon icon-3"></span>
                    <input type="password" name="password" lay-verify="required" placeholder="请输入密码" maxlength="20">
                    <span class="bind-password icon icon-4"></span>
                </div>

                <div class="item">
                    <span class="icon icon-3"></span>
                    <input type="password" name="password_confirmation" lay-verify="required" placeholder="请再次输入密码" maxlength="20">
                    <span class="bind-password icon icon-4"></span>
                </div>

                <div id="validatePanel" class="item" style="width: 137px;">
                    <input type="text" name="captcha" placeholder="请输入验证码" maxlength="4">
                    <img id="refreshCaptcha" class="validateImg" src="{{captcha_src()}}"
                         onclick="this.src=this.src+'?t='+Math.random()">
                </div>
            </div>
            <div class="layui-form-item">
                <button type="submit" class="register-btn" lay-submit="" lay-filter="login">立即注册</button>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('layuimini/lib/layui-v2.5.5/layui.js') }}" charset="utf-8"></script>
<script>
    layui.use(['form', 'jquery'], function () {
        var $ = layui.jquery,
            form = layui.form,
            layer = layui.layer;

        //错误提示
        @if(count($errors)>0)
        @foreach($errors->all() as $error)
        layer.msg("{{$error}}", {icon: 2});
        @break
        @endforeach
        @endif

        // 登录过期的时候，跳出ifram框架
        if (top.location !== self.location) top.location = self.location;

        $('.bind-password').on('click', function () {
            if ($(this).hasClass('icon-5')) {
                $(this).removeClass('icon-5');
                $("input[name='password']").attr('type', 'password');
            } else {
                $(this).addClass('icon-5');
                $("input[name='password']").attr('type', 'text');
            }
        });

        $('.icon-nocheck').on('click', function () {
            if ($(this).hasClass('icon-check')) {
                $(this).removeClass('icon-check');
            } else {
                $(this).addClass('icon-check');
            }
        });
    });
</script>
</body>
</html>
