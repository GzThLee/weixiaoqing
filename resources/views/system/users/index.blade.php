@extends('layouts')

@section('content')
    <fieldset class="table-search-fieldset">
        <legend>搜索</legend>
        <div>
            <form class="layui-form layui-form-pane" action="GET">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">用户账号</label>
                        <div class="layui-input-inline">
                            <input type="text" name="username" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">姓名</label>
                        <div class="layui-input-inline">
                            <input type="text" name="nickname" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">手机号</label>
                        <div class="layui-input-inline">
                            <input type="text" name="mobile" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <button type="button" class="layui-btn layui-btn-primary" lay-submit lay-filter="search"><i class="layui-icon"></i> 搜 索
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </fieldset>

    <table id="dataTable" lay-filter="dataTable"></table>
    <script type="text/html" id="toolbar">
        <a href="{{ route('system.users.create') }}"
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
                , url: "{{ route('system.users.data') }}"
                , toolbar: '#toolbar'
                , defaultToolbar: ['filter', 'exports', 'print']
                , page: true //开启分页
                , limit: 20
                , limits: [20, 40, 80, 100, 150, 300, 500, 1000]
                , cols: [[ //表头
                     {field: 'id', title: 'ID', minWidth: 80}
                    , {field: 'username', title: '用户账号', minWidth: 100}
                    , {field: 'nickname', title: '姓名', minWidth: 100}
                    , {field: 'email', title: '邮箱', minWidth: 120}
                    , {field: 'mobile', title: '电话', minWidth: 120}
                    , {field: 'roles', title: '角色', minWidth: 120}
                    , {field: 'created_at', title: '创建时间', hide: true, minWidth: 170}
                    , {field: 'updated_at', title: '更新时间', minWidth: 170}
                    , {fixed: 'right', title: '操作', width: 120, align: 'center', toolbar: '#options'}
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
                        $.post("{{ route('system.users.destroy') }}", {
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
                    location.href = '/system/users/' + data.id + '/edit';
                }
            });

            //监听指定开关
            form.on('switch(status)', function (data) {
                var id = this.value;
                var status = this.checked ? '1' : '0';
                $.ajax({
                    url: '/admin/user/' + id + '/update',
                    data: {status: status},
                    type: 'PUT'
                }).then(function (resp) {
                    if (resp.code === 0) {
                        layer.msg(resp.msg, {icon: 1});
                    } else {
                        layer.msg(resp.msg, {icon: 2});
                    }
                });
            });

            //监听搜索
            form.on('submit(search)', function (data) {
                dataTable.reload({
                    where: data.field,
                    page: {curr: 1}
                });
                return false;
            });
        })
    </script>
@endsection



