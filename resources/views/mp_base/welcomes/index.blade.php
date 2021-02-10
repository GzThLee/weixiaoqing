@extends('layouts')

@section('title','关注欢迎语设置')

@section('content')
    @if(session('warning_tip'))
        @include('mp_common.warning_tip')
    @endif
    <fieldset class="layui-elem-field layui-field-title">
        <legend>关注欢迎语设置</legend>
    </fieldset>
    <form class="layui-form" action="{{route('mp.base.welcomes.update')}}" method="post">
        {{ csrf_field() }}
        {{ method_field('put') }}
        @include('mp_common.media_tab',[
            "media_type" => ($welcome->media_type ?? \App\Models\Mp::TEXT_MEDIA),
            "data" => $welcome->content ?? (object)[],
            "hide_media_type" => []
        ])
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">保存</button>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        layui.use(['form', 'layedit', 'laydate', 'element','jquery'], function () {
            var form = layui.form
                , $ = layui.jquery
                , layer = layui.layer
                , element = layui.element;

            //监听Tab切换
            element.on('tab(media-tab)', function () {
                $('#media-type').val(this.getAttribute('lay-id'));
            });
        });
    </script>
@endsection
