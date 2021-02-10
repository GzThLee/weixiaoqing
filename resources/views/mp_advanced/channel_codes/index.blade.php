@extends('layouts')

@section('title','渠道二维码')

@section('style')
    <link rel="stylesheet" href="{{ asset('vendor/imgBox/imgbox.css') }}" media="all">
    <style>
        .layui-table-tool, .layui-table-fixed {
            z-index: 1;
        }
    </style>
@endsection

@section('content')
    @if(session('warning_tip'))
        @include('mp_common.warning_tip')
    @endif
    <fieldset class="table-search-fieldset">
        <legend>搜索信息</legend>
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">渠道名称</label>
                    <div class="layui-input-inline">
                        <input type="text" name="name" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <button type="button" class="layui-btn layui-btn-primary" lay-submit lay-filter="searchBtn"><i
                            class="layui-icon"></i> 搜 索
                    </button>
                </div>
            </div>
        </form>
    </fieldset>

    <table id="dataTable" lay-filter="dataTable"></table>

    <script type="text/html" id="toolbar">
        <div class="layui-btn-container">
            <a href="{{ route('mp.advanced.channel_codes.create') }}" class="layui-btn layui-btn-sm">添加</a>
        </div>
    </script>

    <script type="text/html" id="options">
        <button type="button" class="layui-btn layui-btn-sm" lay-event="download">下载</button>
        <button type="button" class="layui-btn layui-btn-sm layui-btn-warm" lay-event="edit">编辑</button>
        <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" lay-event="delete">删除</button>
    </script>

    <script type="text/html" id="record">
        <button type="button" class="layui-btn layui-btn-sm" lay-event="record">查看明细</button>
    </script>

    <script type="text/html" id="qrcode">
        <a class="img-box" href="/storage/channel_codes/@{{ d.m_ccode_id }}_@{{ d.scene_str }}.png">
            <img src="/storage/channel_codes/@{{ d.m_ccode_id }}_@{{ d.scene_str }}.png" height="25" width="25">
        </a>
    </script>
@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('vendor/imgBox/jquery.imgbox.js')}}"></script>
    <script>
        layui.use(['form', 'table', 'jquery', 'layer'], function () {
            var $ = layui.jquery,
                form = layui.form,
                layer = layui.layer,
                table = layui.table;

            var dataTable = table.render({
                elem: '#dataTable',
                url: '{{ route('mp.advanced.channel_codes.data') }}',
                toolbar: '#toolbar',
                defaultToolbar: ['filter', 'exports'],
                cols:
                    [[ //表头
                        {field: 'm_ccode_id', title: '序号ID', width: 80, align: 'center'},
                        {field: 'name', align: 'center', title: '渠道名称', minWidth: 110},
                        {field: 'new_fans_subscribe', align: 'center', title: '新粉丝-关注', minWidth: 110},
                        {field: 'new_fans_unsubscribe', align: 'center', title: '新粉丝-取消', minWidth: 110},
                        {field: 'new_fans_net_increase', align: 'center', title: '新粉丝-净关注', minWidth: 120},
                        {field: 'old_fans_subscribe', align: 'center', title: '老粉丝-关注', minWidth: 110},
                        {field: 'old_fans_unsubscribe', align: 'center', title: '老粉丝-取消', minWidth: 110},
                        {field: 'old_fans_net_increase', align: 'center', title: '老粉丝-净关注', minWidth: 120},
                        {align: 'center', title: '参与二维码', templet: '#qrcode', minWidth: 110},
                        {align: 'center', title: '数据明细', templet: '#record', minWidth: 100},
                        {fixed: 'right', align: 'center', title: '操作', templet: '#options', minWidth: 200}
                    ]],
                limit: 20,
                page: true,
                done: function (res, curr, count) {
                    $(".img-box").imgbox({
                        slideshow: false
                    });
                }
            });

            table.on('tool(dataTable)', function (obj) {
                var data = obj.data;
                if (obj.event === 'edit') {
                    location.href = '/mp_advanced/channel_codes/' + data.m_ccode_id + '/edit';
                } else if (obj.event === 'delete') {
                    layer.confirm('真的删除行么', function (index) {
                        $.ajax({
                            url: '{{ route('mp.advanced.channel_codes.destroy') }}?m_ccode_ids=' + data.m_ccode_id,
                            type: 'delete'
                        }).then(function (res) {
                            if (res.code === 0) {
                                layer.alert(res.msg);
                                layer.close(index);
                                obj.del();
                            } else {
                                layer.alert(res.msg);
                            }
                        });
                    });
                } else if (obj.event === 'download') {
                    location.href = '/mp_advanced/channel_codes/' + data.m_ccode_id + '/download';
                } else if (obj.event === 'record') {
                    location.href = "{{ route('mp.advanced.channel_codes.record') }}" + "?m_ccode_id=" + data.m_ccode_id;
                }
            });

            //监听搜索
            form.on('submit(searchBtn)', function (data) {
                dataTable.reload({
                    where: data.field,
                    page: {curr: 1}
                });
            });

        });
    </script>
@endsection
