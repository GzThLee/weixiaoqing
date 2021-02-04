@extends('layouts')

@section('content')
    <table id="dataTable" lay-filter="dataTable"></table>
    <script type="text/html" id="toolbar">
        <a href="{{ route('system.configs.create') }}"
           class="layui-btn layui-btn-normal layui-btn-sm layui-inline">添加</a>
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
                , url: "{{ route('system.configs.data') }}"
                , toolbar: '#toolbar'
                , defaultToolbar: ['filter', 'exports', 'print']
                , page: true //开启分页
                , limit: 20
                , limits: [20, 40, 80, 100, 150, 300, 500, 1000]
                , cols: [[ //表头
                    {field: 'config_id', title: 'ID', minWidth: 80}
                    , {field: 'display_name', title: '显示名称', minWidth: 100}
                    , {field: 'key_name', title: '英文名称', minWidth: 100}
                    , {field: 'value', title: '配置值', minWidth: 170}
                    , {field: 'created_at', title: '创建时间', minWidth: 170}
                    , {fixed: 'right', title: '操作', width: 180, align: 'center', toolbar: '#options'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    , layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'del') {
                    layer.confirm('确认删除吗？', function (index) {
                        layer.close(index);
                        var load = layer.load();
                        $.post("{{ route('system.configs.destroy') }}", {
                            _method: 'delete',
                            ids: [data.id]
                        }, function (res) {
                            layer.close(load);
                            if (res.code === 0) {
                                layer.msg(res.msg, {icon: 1}, function () {
                                    obj.del();
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2})
                            }
                        });
                    });
                } else if (layEvent === 'edit') {
                    location.href = '/system/configs/' + data.config_id + '/edit';
                }
            });
        })
    </script>
@endsection



