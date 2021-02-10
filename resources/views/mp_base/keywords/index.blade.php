@extends('layouts')

@section('title','关键字回复列表')

@section('content')
    @if(session('warning_tip'))
        @include('mp_common.warning_tip')
    @endif
    <fieldset class="table-search-fieldset">
        <legend>搜索信息</legend>
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">关键字</label>
                    <div class="layui-input-inline">
                        <input type="text" name="keyword" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <button type="button" class="layui-btn layui-btn-primary" lay-submit lay-filter="searchBtn"><i class="layui-icon"></i> 搜 索</button>
                </div>
            </div>
        </form>
    </fieldset>

    <table id="dataTable" lay-filter="dataTable"></table>

    <script type="text/html" id="toolbar">
        <a class="layui-btn layui-btn-sm" href="{{ route('mp.base.keywords.create') }}">添加关键字</a>
    </script>

    <script type="text/html" id="options">
        <a class="layui-btn layui-btn-sm data-count-edit" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-sm layui-btn-danger data-count-delete" lay-event="delete">删除</a>
    </script>
@endsection

@section('script')
    <script>
        layui.use(['form', 'table', 'jquery', 'layer'], function () {
            var $ = layui.jquery,
                form = layui.form,
                layer = layui.layer,
                table = layui.table;

            var dataTable = table.render({
                elem: '#dataTable',
                url: '{{ route('mp.base.keywords.data') }}',
                toolbar: '#toolbar',
                defaultToolbar: ['filter', 'exports'],
                cols:
                    [[ //表头
                        {type: "checkbox", width: 50, fixed: "left"},
                        {field: 'm_kw_id', title: '序号ID', width: 80, align: 'center'},
                        {field: 'keyword', align: 'center', title: '关键词'},
                        {field: 'media_type_name', align: 'center', title: '回复信息类型'},
                        {field: 'trigger_num', align: 'center', title: '触发次数'},
                        {field: 'created_at', align: 'center', title: '创建时间'},
                        {
                            field: 'm_kw_id',
                            align: 'center',
                            title: '操作',
                            minWidth: 100,
                            templet: '#options',
                            fixed: "right"
                        }
                    ]],
                limit: 20,
                page: true
            });

            //监听工具条
            table.on('tool(dataTable)', function (obj) {
                var data = obj.data //获得当前行数据
                    , layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'edit') {
                    location.href = '/mp_base/keywords/' + data.m_kw_id + '/edit';
                } else if (layEvent === 'delete') {
                    layer.confirm('确认删除吗？', function (index) {
                        layer.close(index);
                        var load = layer.load();
                        $.ajax({
                            "url": '{{ route('mp.base.keywords.destroy') }}?m_kw_ids=' + data.m_kw_id,
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
