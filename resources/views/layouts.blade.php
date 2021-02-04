<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('layuimini/lib/layui-v2.5.5/css/layui.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('layuimini/lib/font-awesome-4.7.0/css/font-awesome.min.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('layuimini/css/public.css') }}" media="all">
    @yield('style')

    <script src="{{ asset('layuimini/lib/layui-v2.5.5/layui.js') }}" charset="utf-8"></script>
    <script src="{{ asset('layuimini/js/lay-config.js?v=1.0.4') }}" charset="utf-8"></script>
    <script src="{{asset('vendor/handlebars/js/handlebars.min.js')}}"></script>
</head>
<body>
<blockquote id="iframe-tip" class="layui-elem-quote layui-hide" style="background-color: white;">
    <i class="layui-icon layui-icon-face-surprised"></i> 检测到导航丢失，点击 <button lay-filter="refresh" type="button" class="layui-btn layui-btn-xs" lay-submit="">刷新</button> 找回来吧！
</blockquote>

<div class="layuimini-container">
    <div class="layuimini-main">
        @yield('content')
    </div>
</div>
<script>
    layui.use(['layer','jquery','form'], function () {
        var $ = layui.jquery,
            form = layui.form,
            layer = layui.layer;

        if (self === top) {
            $('#iframe-tip').removeClass('layui-hide');
        }

        form.on('submit(refresh)',function (data){
            var path = '{{ request()->path() }}';
            location.href = '{{ config('app.url') }}' + '/#//' + path;
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //错误提示
        @if(count($errors)>0)
            @foreach($errors->all() as $error)
                layer.msg("{{$error}}", {icon: 2});
                @break
            @endforeach
        @endif

        //一次性正确信息提示
        @if(session('success'))
            layer.msg("{{session('success')}}", {icon: 1});
        @endif

        //一次性错误信息提示
        @if(session('error'))
            layer.msg("{{session('error')}}", {icon: 2});
        @endif

        //一次性提醒信息提示
        @if(session('warning'))
            layer.msg("{{session('warning')}}", {icon: 0});
        @endif

    });
</script>
@yield('script')
</body>
</html>



