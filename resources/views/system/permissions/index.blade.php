@extends('layouts')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group ">
                <a class="layui-btn layui-btn-sm" href="{{ route('system.permissions.create') }}">添加权限</a>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer', 'table', 'form', 'treetable'], function () {
            var $ = layui.jquery,
                layer = layui.layer,
                table = layui.table,
                treetable = layui.treetable;

            // 渲染表格
            var dataTable = function () {
                treetable.render({
                    treeColIndex: 1,          // treetable新增参数
                    treeSpid: 0,             // treetable新增参数
                    treeIdName: 'id',       // treetable新增参数
                    treePidName: 'pid',     // treetable新增参数
                    treeDefaultClose: false,   // treetable新增参数
                    treeLinkage: false,        // treetable新增参数
                    elem: '#dataTable',
                    url: "{{ route('system.permissions.data') }}",
                    where: {model: "permission"},
                    cols: [[ //表头
                        {field: 'id', title: 'ID', sort: true, width: 70}
                        , {field: 'display_name', title: '显示名称', width: 200}
                        , {field: 'name', title: '权限名称', width: 150}
                        , {field: 'route_name', title: '路由', width: 150}
                        , {
                            field: 'icon_id', title: '图标', width: 60, align: 'center', templet: function (d) {
                                return '<i class="fa ' + d.icon + '"></i>';
                            }
                        }
                        , {field: 'type_name', title: '类型', width: 60}
                        , {field: 'sort', title: '排序', edit: 'text', width: 60}
                        , {field: 'created_at', title: '创建时间'}
                        , {field: 'updated_at', title: '更新时间'}
                        , {fixed: 'right', title: '编辑', align: 'center', toolbar: '#options'}
                    ]]
                });
            };

            dataTable(); //调用此函数可重新渲染表格

            //监听单元格编辑
            table.on('edit(dataTable)', function (obj) {
                var value = obj.value //得到修改后的值
                    , data = obj.data //得到所在行所有键值
                    , field = obj.field; //得到字段
                var formData = {};
                formData[field] = value;
                var load = layer.load();
                $.ajax({
                    url: '/system/permissions/' + data.id + '/update',
                    type: 'PUT',
                    data: formData
                }).then(function (res) {
                    layer.close(load);
                    if (res.code === 0) {
                        layer.msg(res.msg, {icon: 1});
                    } else {
                        layer.msg(res.msg, {icon: 2});
                    }
                });
            });

            //监听工具条
            table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    , layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'del') {
                    layer.confirm('确认删除吗？', function (index) {
                        layer.close(index);
                        var load = layer.load();
                        $.post("{{ route('system.permissions.destroy') }}", {
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
                    location.href = '/system/permissions/' + data.id + '/edit';
                }
            });
        })
    </script>
@endsection
