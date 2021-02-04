@extends('layouts')

@section('content')
    <table id="dataTable" lay-filter="dataTable"></table>

    <script type="text/html" id="toolbar">
        <a class="layui-btn layui-btn-warm layui-btn-sm" href="{{ route('work.base.group_chats') }}">返回</a>
    </script>

    <script type="text/html" id="type">
        @{{#  if(d.type == 1){ }}
        <span>成员</span>
        @{{#  }else if(d.type == 2){ }}
        <span>客户</span>
        @{{#  } }}
    </script>

    <script type="text/html" id="name">
        @{{#  if(d.type == 1 && d.user){ }}
        <span>@{{ d.user.name }}</span>
        @{{#  }else if(d.type == 2 && d.customer){ }}
        <span>@{{ d.customer.name }}</span>
        @{{# }else{ }}
        <span style="color: red">非成员客户</span>
        @{{#  } }}
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
                , url: "{{ route('work.base.group_chats.customers.data',['w_gchat_id' => request('w_gchat_id')]) }}"
                , toolbar: '#toolbar'
                , defaultToolbar: ['filter', 'exports', 'print']
                , page: true //开启分页
                , limit: 20
                , limits: [20, 40, 80, 100, 150, 300, 500, 1000]
                , cols: [[ //表头
                    {field: 'w_gchat_id', title: 'ID', hide: true, minWidth: 80}
                    , {field: 'type', title: '类型', templet: '#type', minWidth: 100}
                    , {field: 'name', title: '名字', templet: '#name', minWidth: 100}
                    , {field: 'join_time', title: '入群时间', minWidth: 170}
                    , {field: 'updated_at', title: '更新时间', hide: true, minWidth: 170}
                ]]
            });
        })
    </script>
@endsection



