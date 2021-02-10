@extends('layouts')

@section('content')
    @if(session('warning_tip'))
        @include('mp_common.warning_tip')
    @endif
    <fieldset class="table-search-fieldset">
        <legend>搜索信息</legend>
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">名称</label>
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
        <a class="layui-btn layui-btn-sm" href="{{ route('mp.advanced.short_urls.create') }}">添加</a>
    </script>

    <script type="text/html" id="options">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-sm layui-btn-danger" lay-event="delete">删除</a>
        </div>
    </script>
@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('vendor/imgBox/jquery.imgbox.js')}}"></script>
    <script>
        layui.use(['layer', 'table', 'form', 'jquery'], function () {
            var $ = layui.jquery
                , layer = layui.layer
                , form = layui.form
                , table = layui.table;

            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                , url: "{{ route('mp.advanced.short_urls.data') }}"
                , toolbar: '#toolbar'
                , defaultToolbar: ['filter', 'exports', 'print']
                , page: true //开启分页
                , limit: 20
                , limits: [20, 40, 80, 100, 150, 300, 500, 1000]
                , cols: [[ //表头
                    {field: 'm_short_url_id', title: 'ID', hide: true, minWidth: 80}
                    , {field: 'name', title: '名称', minWidth: 100}
                    , {field: 'long_url', title: '长链接', minWidth: 170}
                    , {field: 'short_url', title: '短链接', minWidth: 50}
                    , {field: 'created_at', title: '创建时间', minWidth: 170}
                    , {field: 'updated_at', title: '更新时间', hide: true, minWidth: 170}
                    , {fixed: 'right', title: '操作', width: 200, align: 'center', toolbar: '#options'}
                ]]
            });


            //监听工具条
            table.on('tool(dataTable)', function (obj) {
                var data = obj.data //获得当前行数据
                    , layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'delete') {
                    layer.confirm('确认删除吗？', function (index) {
                        layer.close(index);
                        var load = layer.load();
                        $.ajax({
                            "url": '{{ route('mp.advanced.short_urls.destroy') }}?ids=' + data.m_short_url_id,
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
        })
    </script>
@endsection



