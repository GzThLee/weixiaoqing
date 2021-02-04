@extends('layouts')

@section('content')
    <table id="dataTable" lay-filter="dataTable"></table>
@endsection

@section('script')
    <script type="text/html" id="avatar">
        @{{# if(d.headimgurl){ }}
        <img src="@{{ d.headimgurl }}" width="25" height="25">
        @{{# } }}
    </script>
    <script>
        var userList = [], fansIds = [{{request('fans_ids')}}];
        layui.use(['form', 'table', 'jquery', 'layer'], function () {
            var $ = layui.jquery,
                table = layui.table;

            table.render({
                elem: '#dataTable',
                url: '{{ route('mp.advanced.templates.users.data') }}',
                toolbar: false,
                cols: [[
                    {type: 'checkbox', fixed: 'left'},
                    {field: 'avatar', align: 'center', templet: '#avatar', title: '头像'},
                    {field: 'nickname', align: 'center', title: '昵称'},
                    {field: 'last_time', align: 'center', title: '最近活跃时间'},
                    {field: 'subscribe_time', align: 'center', title: '关注时间'},
                ]],
                page: false,
                done: function (res, page, count) {
                    userList = res.data;
                    for (var k in res.data) {
                        if ($.inArray(res.data[k].m_fan_id, fansIds) !== -1) {
                            res.data[k]["LAY_CHECKED"] = true;
                            var index = res.data[k]['LAY_TABLE_INDEX'];
                            $('tr[data-index=' + index + '] input[type="checkbox"]').prop('checked', true);
                            $('tr[data-index=' + index + '] input[type="checkbox"]').next().addClass('layui-form-checked');
                        }
                    }
                }
            });

            table.on('checkbox(dataTable)', function (obj) {
                if (obj.type === 'one') {
                    if (obj.checked) {
                        fansIds.push(obj.data.m_fan_id);
                    } else {
                        fansIds.splice($.inArray(obj.data.m_fan_id, fansIds), 1);
                    }
                } else if (obj.type === 'all') {
                    if (obj.checked) {
                        for (var k in userList) {
                            var key = $.inArray(userList[k].m_fan_id, fansIds);
                            if (key === -1) {
                                fansIds.push(userList[k].m_fan_id);
                            }
                        }
                    } else {
                        for (var k in userList) {
                            fansIds.splice($.inArray(userList[k].m_fan_id, fansIds), 1);
                        }
                    }
                }
                $.unique(fansIds);
            });
        });
    </script>
@endsection
