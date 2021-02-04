@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加配置</legend>
    </fieldset>

    <form class="layui-form" action="{{route('system.configs.store')}}" method="post">
        {{csrf_field()}}

        <div class="layui-form-item">
            <label class="layui-form-label">名称：</label>
            <div class="layui-input-inline">
                <input type="text" maxlength="16" name="key_name" lay-filter="key_name"
                       value="{{ old('key_name') ?? '' }}" lay-verify="required"
                       placeholder="请输入名称" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">显示名称：</label>
            <div class="layui-input-inline">
                <input type="text" maxlength="16" name="display_name" lay-filter="display_name"
                       value="{{ old('display_name') ?? '' }}" lay-verify="required"
                       placeholder="请输入显示名称" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">配置值：</label>
            <div class="layui-input-inline">
                <input type="text" maxlength="16" name="value" lay-filter="value"
                       value="{{ old('value') ?? '' }}" lay-verify="required"
                       placeholder="请输入配置值" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn layui-btn-normal" lay-submit lay-filter="submit">确认</button>
                <a class="layui-btn" href="{{route('system.configs')}}">返回</a>
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


