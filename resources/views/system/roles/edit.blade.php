@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>修改用户</legend>
    </fieldset>
    <form class="layui-form" action="{{route('system.roles.update',['id' => $role->id])}}" method="post">
        {{method_field('put')}}
        {{csrf_field()}}
        <div class="layui-form-item">
            <label for="" class="layui-form-label"><span style="color: red"> * </span>名称：</label>
            <div class="layui-input-inline">
                <input type="text" maxlength="16" name="name" lay-filter="name"
                       value="{{ $role->name ?? old('name') }}" lay-verify="required"
                       placeholder="请输入用户名" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label for="" class="layui-form-label"><span style="color: red"> * </span>显示名称：</label>
            <div class="layui-input-inline">
                <input type="text" maxlength="16" name="display_name" lay-filter="display_name"
                       value="{{ $role->display_name ?? old('display_name') }}" lay-verify="required"
                       placeholder="请输入昵称" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">确 认</button>
                <a class="layui-btn" href="{{route('system.roles')}}">返 回</a>
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

