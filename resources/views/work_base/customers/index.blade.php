@extends('layouts')

@section('style')
    <link rel="stylesheet" href="{{ asset('vendor/imgBox/imgbox.css') }}" media="all">
    <style>
        .layui-table-tool, .layui-table-fixed {
            z-index: 1;
        }
    </style>
@endsection

@section('content')
    <fieldset class="table-search-fieldset">
        <legend>搜索信息</legend>
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">客户昵称</label>
                    <div class="layui-input-inline">
                        <input type="text" name="customer_name" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">性别</label>
                    <div class="layui-input-inline">
                        <select name="gender">
                            <option value=""></option>
                            <option value="1">男</option>
                            <option value="2">女</option>
                            <option value="0">未知</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">所属成员</label>
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
                    <button type="button" class="layui-btn layui-btn-primary" lay-submit lay-filter="searchBtn"><i
                            class="layui-icon"></i> 搜 索
                    </button>
                </div>
            </div>
        </form>
    </fieldset>

    <table id="dataTable" lay-filter="dataTable"></table>

    <script type="text/html" id="toolbar">
        <a class="layui-btn layui-btn-sm" href="{{ route('work.base.customer.tags') }}">客户标签</a>
        <button type="button" lay-submit lay-filter="syncCustomer" class="layui-btn layui-btn-sm layui-btn-normal">同步客户</button>
    </script>

    <script type="text/html" id="options">
        <div class="layui-btn-group">
            <a class="layui-btn layui-btn-sm" lay-event="addTags">设置标签</a>
        </div>
    </script>

    <script type="text/html" id="avatar">
        @{{# if(d.customer && d.customer.avatar){ }}
        <a class="img-box" href="@{{ d.customer.avatar }}">
            <img src="@{{ d.customer.avatar }}" width="25" height="25">
        </a>
        @{{# } }}
    </script>

    <script type="text/html" id="gender">
        @{{# if(d.customer && d.customer.gender == 1){ }}
        <span>男</span>
        @{{# }else if(d.customer && d.customer.gender == 2){  }}
        <span>女</span>
        @{{# }else{ }}
        <span>未知</span>
        @{{# } }}
    </script>

    <script type="text/html" id="set-tag-panel">
        <form class="layui-form" lay-filter="tag-form">
            <input type="hidden" name="w_cust_id" value="@{{ d.w_cust_id }}">
            <div class="layui-form-item">
                <label for="" class="layui-form-label">所属成员</label>
                <div class="layui-input-block">
                    <input type="hidden" name="userid" value="@{{ d.user.userid }}" />
                    <div class="layui-form-mid">@{{ d.user.name }}</div>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label">客户昵称</label>
                <div class="layui-input-block">
                    <div class="layui-form-mid">@{{ d.customer.name }}</div>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label">选择标签</label>
                <div class="layui-input-block">
                    @foreach(\App\Models\WorkTagGroup::get() as $tagGroup)
                        @foreach(\App\Models\WorkTag::where('w_tagg_id',$tagGroup->w_tagg_id)->get() as $key => $tag)
                            <input type="checkbox" name="w_tag_id[{{$tag->w_tag_id}}]" value="{{$tag->w_tag_id}}" title="{{$tagGroup->name}} | {{$tag->name}}">
                        @endforeach
                    @endforeach
                </div>
            </div>
        </form>
    </script>
@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('vendor/imgBox/jquery.imgbox.js')}}"></script>
    <script>
        layui.use(['jquery', 'layer', 'table', 'form', 'laytpl'], function () {
            var $ = layui.jquery
                , layer = layui.layer
                , form = layui.form
                , table = layui.table
                , laytpl = layui.laytpl;

            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable',
                url: "{{ route('work.base.customers.data') }}",
                toolbar: '#toolbar',
                defaultToolbar: ['filter', 'exports', 'print'],
                page: true, //开启分页
                limit: 20,
                limits: [20, 40, 80, 100, 150, 300, 500, 1000],
                cols: [[ //表头
                    {field: 'w_cust_follow_id', title: 'ID', hide: true, minWidth: 80}
                    , {field: 'external_userid', templet: function (data) {
                            return data.customer ? data.customer.external_userid : '';
                        }, title: 'userid', hide: true, minWidth: 100}
                    , {field: 'avatar', title: '头像', templet: '#avatar', minWidth: 50}
                    , {field: 'name', title: '客户昵称', templet: function (data) {
                            return data.customer ? data.customer.name : '';
                        }, minWidth: 100}
                    , {field: 'gender', title: '性别', templet: '#gender', minWidth: 100}
                    , {field: 'unionid', title: 'unionid', templet: function (data) {
                            return data.customer ? data.customer.unionid : '';
                        }, hide: true, minWidth: 100}
                    , {
                        field: 'follow_user', title: '所属成员', templet: function (data) {
                            return data.follow_user ? data.follow_user.name : '';
                        }, minWidth: 100
                    }
                    , {field: 'create_time', title: '添加时间', minWidth: 170}
                    , {title: '操作', align: 'center', toolbar: '#options'}
                ]],
                done: function (res, curr, count) {
                    $(".img-box").imgbox({
                        slideshow: false
                    });
                }
            });

            //监听工具条
            table.on('tool(dataTable)', function (obj) {
                var data = obj.data
                    , layEvent = obj.event;
                if (layEvent === 'addTags') {
                    var temp = $('#set-tag-panel').html(), tempHtml = '';
                    laytpl(temp).render(data, function (html) {
                        tempHtml = html;
                    });

                    layer.open({
                        title: '设置标签',
                        offset: 't',
                        area: ['500px', '450px'],
                        content: tempHtml,
                        btn: ['确认', '取消'] //可以无限个按钮
                        , yes: function (index, layero) {
                            $.ajax({
                                url: "{{ route('work.base.customers.tags.update') }}",
                                data: form.val("tag-form"),
                                type: 'PUT'
                            }).then(function (res) {
                                if (res.code === 0) {
                                    layer.close(index);
                                    dataTable.reload();
                                    layer.msg(res.msg);
                                }
                            })
                        }
                        , btn2: function (index, layero) {
                            layer.close(index)
                        }
                        , success: function (layero, index) {
                            for (var key in data.customer_tags) {
                                var w_tag_id = data.customer_tags[key].w_tag_id;
                                $('input[name="w_tag_id[' + w_tag_id + ']"]').attr("checked", "true");
                            }
                            form.render();
                        }
                    });
                }
            });

            form.on('submit(syncCustomer)', function () {
                var load = layer.load();
                $.ajax({
                    url: "{{ route('work.base.info.command') }}",
                    type: 'POST',
                    data: {command: 'sync:work:customers'},
                }).then(function (resp) {
                    layer.close(load);
                    layer.alert(resp.msg, function () {
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



