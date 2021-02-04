@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>添加用户</legend>
    </fieldset>

    <form class="layui-form" action="{{route('system.users.store')}}" method="post">
        {{csrf_field()}}

        <div class="layui-form-item">
            <label class="layui-form-label"><span style="color: red"> * </span>用户账号：</label>
            <div class="layui-input-inline">
                <input type="text" maxlength="16" name="username"
                       value="{{ old('username') ?? '' }}" lay-verify="required"
                       placeholder="请输入用户名" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><span style="color: red"> * </span>姓名：</label>
            <div class="layui-input-inline">
                <input type="text" maxlength="16" name="nickname"
                       value="{{ old('nickname') ?? '' }}" lay-verify="required"
                       placeholder="请输入昵称" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><span style="color: red"> * </span>手机号：</label>
            <div class="layui-input-inline">
                <input type="text" name="mobile" value="{{ old('mobile') ?? '' }}"
                       lay-verify="phone"
                       placeholder="请输入手机号" class="layui-input" lay-verify="required">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">邮箱：</label>
            <div class="layui-input-inline">
                <input type="text" name="email" value="{{ old('email') ?? '' }}"
                       placeholder="请输入邮箱" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><span style="color: red"> * </span>密码：</label>
            <div class="layui-input-inline">
                <input type="password" name="password" placeholder="请输入密码" class="layui-input"
                       lay-verify="required">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><span style="color: red"> * </span>确认密码：</label>
            <div class="layui-input-inline">
                <input type="password" name="password_confirmation" placeholder="请输入密码" class="layui-input"
                       lay-verify="required">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">权限角色：</label>
            <div class="layui-input-inline">
                @forelse($roles as $role)
                    <input type="checkbox" name="roles[]" value="{{$role->id}}"
                           title="{{$role->display_name}}" {{ $role->own ? 'checked' : ''  }} >
                @empty
                    <div class="layui-form-mid layui-word-aux">还没有角色</div>
                @endforelse
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn layui-btn-normal" lay-submit lay-filter="submit">确认</button>
                <a class="layui-btn" href="{{route('system.users')}}">返回</a>
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


