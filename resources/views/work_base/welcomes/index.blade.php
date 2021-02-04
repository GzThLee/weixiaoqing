@extends('layouts')

@section('content')
    <table id="dataTable" lay-filter="dataTable"></table>
    <script type="text/html" id="toolbar">
        <a class="layui-btn layui-btn-sm" href="{{ route('work.base.welcomes.create') }}">添加欢迎语</a>
    </script>
    <script type="text/html" id="options">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
        </div>
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
    <script>
        layui.use(['layer', 'table', 'form'], function () {
            var $ = layui.jquery
                , layer = layui.layer
                , form = layui.form
                , table = layui.table;

            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                , url: "{{ route('work.base.welcomes.data') }}"
                , toolbar: '#toolbar'
                , defaultToolbar: ['filter', 'exports', 'print']
                , page: true //开启分页
                , limit: 20
                , limits: [20, 40, 80, 100, 150, 300, 500, 1000]
                , cols: [[ //表头
                    {field: 'w_wlcm_id', title: 'ID', hide: true, minWidth: 80}
                    , {field: 'name', title: '名字', minWidth: 100}
                    , {field: 'username', title: '成员名字', templet: function (data) {
                            return data.user ? data.user.name : '';
                        }, minWidth: 100}
                    , {field: 'type', title: '回复类型', templet:'#type', minWidth: 170}
                    , {field: 'created_at', title: '创建时间', minWidth: 170}
                    , {field: 'updated_at', title: '更新时间', hide: true, minWidth: 170}
                    , {fixed: 'right', title: '操作', width: 100, align: 'center', toolbar: '#options'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    , layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'edit') {
                    location.href = '/work_base/welcomes/' + data.w_wlcm_id + '/edit';
                }
            });
        })
    </script>
@endsection



