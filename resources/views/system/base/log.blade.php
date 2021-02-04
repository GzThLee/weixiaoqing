@extends('layouts')

@section('content')
    <table id="dataTable" lay-filter="dataTable"></table>
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
                , url: "{{ route('system.logs.data') }}"
                , toolbar: true
                , defaultToolbar: ['filter', 'exports', 'print']
                , page: true //开启分页
                , limit: 20
                , limits: [20, 40, 80, 100, 150, 300, 500, 1000]
                , cols: [[ //表头
                    {field: 'log_id', title: 'ID', minWidth: 80}
                    , {field: 'user_id', title: '用户ID', minWidth: 100}
                    , {field: 'route_name', title: '路由名称', minWidth: 100}
                    , {field: 'ip', title: 'IP', minWidth: 100}
                    , {field: 'user_agent', title: '设备标识', minWidth: 100}
                    , {field: 'uri', title: '链接', minWidth: 100}
                    , {field: 'parameter', title: '参数', minWidth: 100}
                    , {field: 'method', title: '请求方法', minWidth: 170}
                    , {field: 'created_at', title: '创建时间', minWidth: 170}
                ]]
            });
        })
    </script>
@endsection



