@extends('layouts')

@section('title','模板消息列表')

@section('content')
    @if(session('warning_tip'))
        @include('mp_common.warning_tip')
    @endif
    <fieldset class="table-search-fieldset">
        <legend>搜索信息</legend>
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">推送主题</label>
                    <div class="layui-input-inline">
                        <input type="text" name="theme" autocomplete="off" class="layui-input">
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

    <script type="text/html" id="options">
        <a class="layui-btn layui-btn-sm" lay-event="edit">重用</a>
        <a class="layui-btn layui-btn-sm layui-btn-danger" lay-event="delete">删除</a>
    </script>

    <script type="text/html" id="toolbar">
        <div class="layui-btn-container">
            <a class="layui-btn layui-btn-sm" href="{{ route('mp.advanced.templates.create') }}">添加</a>
        </div>
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
                url: '{{ route('mp.advanced.templates.data') }}',
                toolbar: '#toolbar',
                defaultToolbar: ['filter', 'exports'],
                cols:
                    [[ //表头
                        {field: 'm_temp_id', align: 'center', title: '序号ID', width: 80},
                        {field: 'theme', align: 'center', title: '推送主题'},
                        {field: 'created_at', align: 'center', title: '创建时间'},
                        {field: 'push_time', align: 'center', title: '发送时间'},
                        {field: 'finish_time', align: 'center', title: '发送完成时间'},
                        {field: 'send_count', align: 'center', title: '发送人数'},
                        {field: 'send_success_count', align: 'center', title: '发送成功人数'},
                        {field: 'send_fail_count', align: 'center', title: '发送失败人数'},
                        {fixed: 'right', align: 'center', title: '操作', templet: '#options'}
                    ]],
                limits: [20, 40, 80, 100, 150, 300, 500, 1000],
                limit: 20,
                page: true
            });

            table.on('tool(dataTable)', function (obj) {
                var data = obj.data;
                if (obj.event === 'edit') {
                    location.href = '/mp_advanced/templates/' + data.m_temp_id + '/edit';
                } else if (obj.event === 'delete') {
                    layer.confirm('真的删除行么', function (index) {
                        $.ajax({
                            url: '{{ route('mp.advanced.templates.destroy') }}?m_temp_ids=' + data.m_temp_id,
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
