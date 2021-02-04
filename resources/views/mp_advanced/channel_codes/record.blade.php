@extends('layouts')

@section('title','二维码明细')

@section('content')
    <table id="dataTable" lay-filter="dataTable"></table>

    <script type="text/html" id="toolbar">
        <div class="layui-btn-container">
            <a href="javascript:history.go(-1)" class="layui-btn layui-btn-normal layui-btn-sm"> 返回</a>
        </div>
    </script>
@endsection

@section('script')
    <script>
        layui.use(['form', 'table'], function () {
            var $ = layui.jquery,
                form = layui.form,
                table = layui.table;

            table.render({
                elem: '#dataTable',
                toolbar: '#toolbar',
                url: '{{ route('mp.advanced.channel_codes.record.data',['m_ccode_id' => request('m_ccode_id')]) }}',
                defaultToolbar: ['filter', 'exports'],
                cols:
                    [[ //表头
                        {field: 'date', align: 'center', title: '日期'},
                        {field: 'subscribe_num', align: 'center', title: '新增关注'},
                        {field: 'old_fans_num', align: 'center', title: '旧粉丝'},
                        {field: 'net_growth', align: 'center', title: '净增长'},
                        {field: 'unsubscribe_num', align: 'center', title: '取消关注'}
                    ]],
                limit: 10000,
                page: false
            });
        });
    </script>
@endsection
