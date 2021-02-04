@extends('layouts')

@section('style')
    <link rel="stylesheet" href="{{ asset('vendor/imgBox/imgbox.css') }}" media="all">
    <style>
        .layui-table-tool,.layui-table-fixed{
            z-index: 1;
        }
    </style>
@endsection

@section('content')
    <table id="dataTable" lay-filter="dataTable"></table>
    <script type="text/html" id="toolbar">
        <a class="layui-btn layui-btn-sm" href="{{ route('work.advanced.channel_codes.create') }}">添加</a>
    </script>
    <script type="text/html" id="options">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="record">记录</a>
            <a class="layui-btn layui-btn-sm layui-btn-danger" lay-event="delete">删除</a>
        </div>
    </script>
    <script type="text/html" id="qrcode">
        <a class="img-box" href="@{{ d.qr_code_url }}">
            <img src="@{{ d.qr_code_url }}" height="25" width="25">
        </a>
    </script>
    <script type="text/html" id="type">
        @{{# if(d.welcome_type == 0){ }}
        <span>纯文字</span>
        @{{# } else if(d.welcome_type == 1){ }}
        <span>文字+图片</span>
        @{{# } else if(d.welcome_type == 2){ }}
        <span>文字+图文链接</span>
        @{{# } else if(d.welcome_type == 3){ }}
        <span>文字+小程序</span>
        @{{# } }}
    </script>
@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('vendor/imgBox/jquery.imgbox.js')}}"></script>
    <script>
        layui.use(['layer', 'table', 'form','jquery'], function () {
            var $ = layui.jquery
                , layer = layui.layer
                , form = layui.form
                , table = layui.table;

            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                , url: "{{ route('work.advanced.channel_codes.data') }}"
                , toolbar: '#toolbar'
                , defaultToolbar: ['filter', 'exports', 'print']
                , page: true //开启分页
                , limit: 20
                , limits: [20, 40, 80, 100, 150, 300, 500, 1000]
                , cols: [[ //表头
                    {field: 'w_ccode_id', title: 'ID', hide: true, minWidth: 80}
                    , {field: 'name', title: '名字', minWidth: 100}
                    , {field: 'username', title: '成员名字', templet: function (data) {
                            return data.user ? data.user.name : 'undefined';
                        }, minWidth: 100}
                    , {field: 'type', title: '回复类型', templet:'#type', minWidth: 170}
                    , {field: 'add_number', title: '添加人数', minWidth: 50}
                    , {field: 'end_time', title: '过期时间', minWidth: 170}
                    , {field: 'created_at', title: '创建时间', minWidth: 170}
                    , {field: 'updated_at', title: '更新时间', hide: true, minWidth: 170}
                    , {title: '参与二维码',align: 'center', templet: '#qrcode', minWidth: 100}
                    , {fixed: 'right', title: '操作', width: 200, align: 'center', toolbar: '#options'}
                ]],
                done: function(res, curr, count){
                    $(".img-box").imgbox({
                        slideshow:false
                    });
                }
            });



            //监听工具条
            table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    , layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'edit') {
                    location.href = '/work_advanced/channel_codes/' + data.w_ccode_id + '/edit';
                } else if (layEvent === 'record') {
                    location.href = '/work_advanced/channel_codes/' + data.w_ccode_id + '/record';
                } else if (layEvent === 'delete') {
                layer.confirm('确认删除吗？', function (index) {
                    layer.close(index);
                    var load = layer.load();
                    $.ajax({
                        "url": '{{ route('work.advanced.channel_codes.destroy') }}?ids=' + data.w_ccode_id,
                        "type": 'delete'
                    }).then(function (resp) {
                        layer.close(load);
                        if (resp.code === 0) {
                            layer.msg(resp.msg, {icon: 1}, function () {
                                obj.del();
                            })
                        } else {
                            layer.msg(resp.msg, {icon: 2})
                        }
                    });
                });
            }
            });
        })
    </script>
@endsection



