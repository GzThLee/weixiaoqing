@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>修改密码</legend>
    </fieldset>
    <form class="layui-form" action="{{route('users.changePasswordPut')}}" method="post">
        {{method_field('put')}}
        {{csrf_field()}}
        <div class="layui-form-item">
            <label for="" class="layui-form-label">密码：</label>
            <div class="layui-input-inline">
                <input type="password" name="password" placeholder="请输入旧密码" class="layui-input" lay-verify="required" />
            </div>
        </div>

        <div class="layui-form-item">
            <label for="" class="layui-form-label">新密码：</label>
            <div class="layui-input-inline">
                <input type="password" name="new_password" placeholder="请输入新密码" class="layui-input"
                       lay-verify="required" min="8" />
            </div>
        </div>

        <div class="layui-form-item">
            <label for="" class="layui-form-label">确认新密码：</label>
            <div class="layui-input-inline">
                <input type="password" name="new_password_confirmation" placeholder="请输入新密码" class="layui-input" lay-verify="required" min="8" />
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">确 认</button>
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

