@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>公众号-基础配置</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('mp.base.info.save') }}" method="POST">
        {{ csrf_field() }}
        <div class="layui-form-item">
            <label class="layui-form-label">app_id:</label>
            <div class="layui-input-block">
                <input type="text" name="app_id" lay-verify="required" value="{{ $mp->app_id ?? '' }}"
                       placeholder="请输入appid" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">app_secret:</label>
            <div class="layui-input-block">
                <input type="text" name="app_secret" lay-verify="required" value="{{ $mp->app_secret ?? '' }}"
                       placeholder="请输入app_secret" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">URL:</label>
            <div class="layui-input-block">
                <input type="text" value="{{ route('api.mp.event.api',['api_token' => $mp->api_token]) }}" disabled
                       class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">Token:</label>
            <div class="layui-input-block">
                <input type="text" name="valid_token" value="{{ $mp->valid_token ?? '' }}" placeholder="校验token"
                       class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">AESKey:</label>
            <div class="layui-input-block">
                <input type="text" name="encodingaeskey" value="{{ $mp->encodingaeskey ?? '' }}"
                       placeholder="校验EncodingAESKey" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">IP白名单:</label>
            <div class="layui-input-block">
                <input type="text" value="{{ system_config('server_ip') }}" disabled class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">保存</button>
            </div>
        </div>
    </form>
    <fieldset class="layui-elem-field layui-field-title">
        <legend>公众号类型功能列表</legend>
    </fieldset>
    <blockquote class="layui-elem-quote layui-text">因公众号类型不同，权限功能不同，请查阅<a
            href="https://developers.weixin.qq.com/doc/offiaccount/Getting_Started/Explanation_of_interface_privileges.html"
            target="_blank">《官方文档》</a>
    </blockquote>
    <table class="layui-table">
        <thead>
        <tr>
            <th>功能模块</th>
            <th>功能名称</th>
            <th>是否拥有</th>
        </tr>
        </thead>
        <tbody>
        @foreach($permissions as $permission)
            @foreach($permission->childs as $childPermission)
                <tr>
                    <td>{{$permission->display_name}}</td>
                    <td>{{$childPermission->display_name}}</td>
                    <td>
                        @if(in_array($childPermission->id,$mpPermissionIds))
                            <span style="color: #5FB878">有</span>
                        @else
                            <span style="color: #FF5722">无</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
@endsection

@section('script')
    <script>
        layui.use(['layer', 'table', 'form', 'jquery'], function () {
            var $ = layui.jquery
                , layer = layui.layer
                , form = layui.form;

            form.on('submit(submit)', function () {
                layer.load();
            });
        })
    </script>
@endsection



