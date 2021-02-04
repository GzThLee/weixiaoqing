@extends('layouts')

@section('title','粉丝标签列表')

@section('content')
    <table id="dataTable" lay-filter="dataTable"></table>

    <script type="text/html" id="toolbar">
        <a class="layui-btn layui-btn-sm" href="{{ route('mp.base.fans.tags.create') }}">添加标签</a>
        <a class="layui-btn layui-btn-sm layui-btn-normal" href="{{ route('mp.base.fans') }}">粉丝列表</a>
    </script>

    <script type="text/html" id="options">
        <button class="layui-btn layui-btn-sm data-count-edit" lay-event="edit">编辑</button>
        <button class="layui-btn layui-btn-sm layui-btn-danger data-count-delete" lay-event="delete">删除</button>
    </script>
@endsection

@section('script')
    <script>
        layui.use(['form', 'table', 'layer', 'jquery'], function () {
            var $ = layui.jquery,
                form = layui.form,
                layer = layui.layer,
                table = layui.table;

            table.render({
                elem: '#dataTable',
                url: '{{ route('mp.base.fans.tags.data') }}',
                toolbar: '#toolbar',
                defaultToolbar: ['filter', 'exports', 'print', {
                    title: '提示',
                    layEvent: 'LAYTABLE_TIPS',
                    icon: 'layui-icon-tips'
                }],
                cols: [[
                    {field: 'm_tag_id', title: '序号ID', width: 80, align: 'center'},
                    {field: 'tag_id', title: '微信标签ID', width: 120, align: 'center'},
                    {field: 'name', align: 'center', title: '组名'},
                    {field: 'description', align: 'center', title: '备注'},
                    {field: 'fans_num', align: 'center', title: '人数'},
                    {align: 'center', title: '操作', toolbar: '#options'}
                ]],
                limit: 20,
                page: true
            });

            //监听工具条
            table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    , layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'edit') {
                    location.href = '/mp_base/fans/tags/' + data.m_tag_id + '/edit';
                } else if (layEvent === 'delete') {
                    layer.confirm('确认删除吗？', function (index) {
                        layer.close(index);
                        var load = layer.load();
                        $.ajax({
                            "url": '{{ route('mp.base.fans.tags.destroy') }}?m_tag_ids=' + data.m_tag_id,
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
        });
    </script>
@endsection
