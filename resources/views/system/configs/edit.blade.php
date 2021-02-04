@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>修改配置</legend>
    </fieldset>
    <form class="layui-form" action="{{route('system.configs.update',['id' => $config->config_id])}}" method="post">
        {{method_field('put')}}
        {{csrf_field()}}
        <div class="layui-form-item">
            <label for="" class="layui-form-label">名称：</label>
            <div class="layui-input-inline">
                <input type="text" maxlength="16" name="key_name" lay-filter="key_name"
                       value="{{ $config->key_name ?? old('key_name') }}" lay-verify="required"
                       placeholder="请输入名称" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label for="" class="layui-form-label">显示名称：</label>
            <div class="layui-input-inline">
                <input type="text" maxlength="16" name="display_name" lay-filter="display_name"
                       value="{{ $config->display_name ?? old('display_name') }}" lay-verify="required"
                       placeholder="请输入显示名称" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label for="" class="layui-form-label">配置值：</label>
            <div class="layui-input-inline">
                <input type="text" maxlength="16" name="value" lay-filter="value"
                       value="{{ $config->value ?? old('value') }}" lay-verify="required"
                       placeholder="请输入配置值" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">确 认</button>
                <a class="layui-btn" href="{{route('system.configs')}}">返 回</a>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        layui.use(['element', 'form'], function () {
            var $ = layui.jquery;

        })
    </script>
@endsection

