@extends('layouts')

@section('content')
    <table id="dataTable" lay-filter="dataTable"></table>
    <script type="text/html" id="toolbar">
        <button type="button" lay-submit lay-filter="syncUser" class="layui-btn layui-btn-sm layui-btn-normal">同步成员</button>
    </script>
    <script type="text/html" id="options">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
        </div>
    </script>
@endsection

@section('script')
    <script>
        layui.use(['layer', 'table', 'form'], function () {
            var $ = layui.jquery
                , layer = layui.layer
                , form = layui.form
                , table = layui.table;

            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                , url: "{{ route('work.base.users.data') }}"
                , toolbar: '#toolbar'
                , defaultToolbar: ['filter', 'exports', 'print']
                , page: true //开启分页
                , limit: 20
                , limits: [20, 40, 80, 100, 150, 300, 500, 1000]
                , cols: [[ //表头
                    {field: 'w_user_id', title: 'ID', hide: true, minWidth: 80}
                    , {field: 'userid', title: 'userid', minWidth: 100}
                    , {field: 'name', title: '名字', minWidth: 100}
                    , {field: 'open_userid', title: 'open_userid', minWidth: 100}
                    , {field: 'updated_at', title: '更新时间', minWidth: 170}
                    , {field: 'created_at', title: '创建时间', minWidth: 170}
                ]]
            });

            form.on('submit(syncUser)', function () {
                var load = layer.load();
                $.ajax({
                    url: "{{ route('work.base.info.command') }}",
                    type: 'POST',
                    data: {command:'sync:work:users'},
                }).then(function (resp) {
                    layer.close(load);
                    layer.alert(resp.msg,function () {
                        dataTable.reload();
                        layer.closeAll();
                    });
                });
                return false;
            });
        })
    </script>
@endsection



