@extends('layouts')

@section('content')
    <fieldset class="table-search-fieldset">
        <legend>搜索信息</legend>
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">群主</label>
                    <div class="layui-input-inline">
                        <select name="follow_user_id">
                            <option value=""></option>
                            @foreach($follow_users as $follow_user)
                                <option value="{{ $follow_user->userid }}">{{ $follow_user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">客户昵称</label>
                    <div class="layui-input-inline">
                        <input type="text" name="customer_name" autocomplete="off" class="layui-input">
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
        <button type="button" lay-submit lay-filter="syncGroupChat" class="layui-btn layui-btn-sm layui-btn-normal">同步客户群</button>
    </script>

    <script type="text/html" id="options">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-sm" lay-event="customers">客户明细</a>
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
                , url: "{{ route('work.base.group_chats.data') }}"
                , toolbar: '#toolbar'
                , defaultToolbar: ['filter', 'exports', 'print']
                , page: true //开启分页
                , limit: 20
                , limits: [20, 40, 80, 100, 150, 300, 500, 1000]
                , cols: [[ //表头
                    {field: 'w_gchat_id', title: 'ID', hide: true, minWidth: 80}
                    , {field: 'chat_id', title: 'chat_id',hide: true, minWidth: 100}
                    , {field: 'name', title: '名字', minWidth: 100}
                    , {field: 'owner_name', title: '群主', templet: function (data) {
                            return data.owner ? data.owner.name : '未知成员';
                        }, minWidth: 100}
                    , {field: 'customer_num', title: '客户人数', minWidth: 170}
                    , {field: 'create_time', title: '创建时间', minWidth: 170}
                    , {field: 'updated_at', title: '更新时间', hide: true, minWidth: 170}
                    , {fixed: 'right', title: '操作', width: 100, align: 'center', toolbar: '#options'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    , layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'customers') {
                    location.href = '{{route('work.base.group_chats.customers')}}?w_gchat_id=' + data.w_gchat_id;
                }
            });

            form.on('submit(syncGroupChat)', function () {
                var load = layer.load();
                $.ajax({
                    url: "{{ route('work.base.info.command') }}",
                    type: 'POST',
                    data: {command:'sync:work:group:chats'},
                }).then(function (resp) {
                    layer.close(load);
                    layer.alert(resp.msg,function () {
                        dataTable.reload();
                        layer.closeAll();
                    });
                });
                return false;
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



