@extends('layouts')

@section('style')
    <link rel="stylesheet" href="{{ asset('vendor/imgBox/imgbox.css') }}" media="all">
    <style>
        .layui-table-tool,.layui-table-fixed{
            z-index: 1;
        }
    </style>
@endsection

@section('content')
    <table id="dataTable" lay-filter="dataTable"></table>
    <script type="text/html" id="toolbar">
        <a class="layui-btn layui-btn-sm" href="{{ route('work.advanced.channel_codes') }}">返回</a>
    </script>
    <script type="text/html" id="avatar">
        @{{# if(d.customer){ }}
        <a class="img-box" href="@{{ d.customer.avatar }}">
            <img src="@{{ d.customer.avatar }}" width="25" height="25">
        </a>
        @{{# } }}
    </script>
@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('vendor/imgBox/jquery.imgbox.js')}}"></script>
    <script>
        layui.use(['layer', 'table', 'form','jquery'], function () {
            var $ = layui.jquery
                , layer = layui.layer
                , form = layui.form
                , table = layui.table;

            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                , url: "{{ route('work.advanced.channel_codes.record.data',['w_ccode_id' => $workChannelCode->w_ccode_id]) }}"
                , toolbar: '#toolbar'
                , defaultToolbar: ['filter', 'exports', 'print']
                , page: true //开启分页
                , limit: 20
                , limits: [20, 40, 80, 100, 150, 300, 500, 1000]
                , cols: [[ //表头
                    {field: 'w_ccode_cust_id', title: 'ID', hide: true, minWidth: 80}
                    , {field: 'avatar', title: '头像', templet: '#avatar', minWidth: 100}
                    , {field: 'name', title: '客户名称', templet: function (data) {
                            return data.customer ? data.customer.name : '';
                        }, minWidth: 100}
                    , {field: 'name', title: 'unionid', templet: function (data) {
                            return data.customer ? data.customer.unionid : '';
                        }, minWidth: 100}
                    , {field: 'created_at', title: '创建时间', minWidth: 170}
                    , {field: 'updated_at', title: '更新时间', hide: true, minWidth: 170}
                ]]
                ,done: function(res, curr, count){
                    $(".img-box").imgbox({
                        slideshow:false
                    });
                }
            });
        })
    </script>
@endsection



