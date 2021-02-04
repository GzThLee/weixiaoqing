@extends('layouts')

@section('content')

    <fieldset class="layui-elem-field layui-field-title">
        <legend>企业微信-基础配置</legend>
    </fieldset>

    <form class="layui-form" action="{{ route('work.base.info.save') }}" method="POST">
        {{ csrf_field() }}
        <div class="layui-form-item">
            <label class="layui-form-label">corpid:</label>
            <div class="layui-input-block">
                <input type="text" name="corpid" lay-verify="required" value="{{ $work->corpid ?? '' }}"
                       placeholder="请输入corpid" class="layui-input">
            </div>
        </div>
        <fieldset class="layui-elem-field layui-field-title">
            <legend>客户联系配置</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">Secret:</label>
            <div class="layui-input-block">
                <input type="text" name="corpsecret" lay-verify="required" value="{{ $work->corpsecret ?? '' }}"
                       placeholder="请输入corpsecret" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">URL:</label>
            <div class="layui-input-block">
                <input type="text" value="{{ route('api.work.event.api',['api_token' => $work->api_token]) }}" disabled
                       class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">Token:</label>
            <div class="layui-input-block">
                <input type="text" name="token" value="{{ $work->token ?? '' }}" placeholder="请输入token"
                       class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">Encoding<br/>AESKey:</label>
            <div class="layui-input-block">
                <input type="text" name="aes_key" value="{{ $work->aes_key ?? '' }}" placeholder="请输入aes_key"
                       class="layui-input">
            </div>
        </div>
        <fieldset class="layui-elem-field layui-field-title">
            <legend>通信录配置</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">Secret:</label>
            <div class="layui-input-block">
                <input type="text" name="usersecret" lay-verify="required" value="{{ $work->usersecret ?? '' }}"
                       placeholder="请输入usersecret" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">保存</button>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        layui.use(['layer', 'table', 'form'], function () {
            var $ = layui.jquery
                , layer = layui.layer
                , form = layui.form;
        })
    </script>
@endsection



