@extends('layouts')

@section('title','粉丝列表')

@section('style')
    <style>
        .layui-form-checkbox {
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    @if(session('warning_tip'))
        @include('mp_common.warning_tip')
    @endif
    <fieldset class="table-search-fieldset">
        <legend>搜索信息</legend>
        <form class="layui-form layui-form-pane" action="">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">昵称</label>
                    <div class="layui-input-inline">
                        <input type="text" name="nickname" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">标签</label>
                    <div class="layui-input-inline">
                        <select name="m_tag_id" lay-filter="m_tag_id">
                            <option value=""></option>
                            @foreach($tags as $mTagId => $name)
                                <option value="{{$mTagId}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">备注</label>
                    <div class="layui-input-inline">
                        <input type="text" name="remark" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">地区</label>
                    <div class="layui-input-inline">
                        <input type="text" name="area" autocomplete="off" class="layui-input" placeholder="国家/省/市" >
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">排序规则</label>
                    <div class="layui-input-inline">
                        <select name="sort_field" lay-filter="sort_field">
                            <option value="last_time" selected>活跃时间倒序</option>
                            <option value="subscribe_time">关注时间倒序</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">是否关注</label>
                    <div class="layui-input-inline">
                        <select name="is_subscribe" lay-filter="is_subscribe">
                            <option value=""></option>
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">来源渠道</label>
                    <div class="layui-input-inline">
                        <select name="subscribe_scene" lay-filter="subscribe_scene">
                            <option value=""></option>
                            <option value="ADD_SCENE_SEARCH">公众号搜索</option>
                            <option value="ADD_SCENE_ACCOUNT_MIGRATION">公众号迁移</option>
                            <option value="ADD_SCENE_PROFILE_CARD">名片分享</option>
                            <option value="ADD_SCENE_QR_CODE">扫描二维码</option>
                            <option value="ADD_SCENE_PROFILE_LINK">图文页内名称点击</option>
                            <option value="ADD_SCENE_PROFILE_ITEM">图文页右上角菜单</option>
                            <option value="ADD_SCENE_PAID">支付后关注</option>
                            <option value="ADD_SCENE_WECHAT_ADVERTISEMENT">微信广告</option>
                            <option value="ADD_SCENE_OTHERS">其他</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">活跃时间</label>
                    <div class="layui-input-inline">
                        <input type="text" name="active_start_time" id="active-start-time" placeholder="开始时间" lay-filter="start_time" class="layui-input" autocomplete="off">
                    </div>
                    <div class="layui-form-mid"> -</div>
                    <div class="layui-input-inline">
                        <input type="text" name="active_end_time" id="active-end-time" value="" placeholder="结束时间" lay-filter="end_time" class="layui-input" autocomplete="off">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">关注时间</label>
                    <div class="layui-input-inline">
                        <input type="text" name="subscribe_start_time" id="subscribe-start-time" value="" placeholder="开始时间" lay-filter="start_time" class="layui-input" autocomplete="off">
                    </div>
                    <div class="layui-form-mid"> -</div>
                    <div class="layui-input-inline">
                        <input type="text" name="subscribe_end_time" id="subscribe-end-time" value="" placeholder="结束时间" lay-filter="end_time" class="layui-input" autocomplete="off">
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
        <a class="layui-btn layui-btn-sm" href="{{ route('mp.base.fans.tags') }}">粉丝标签</a>
        <button type="button" lay-submit lay-filter="syncFans" class="layui-btn layui-btn-sm layui-btn-normal">同步粉丝
        </button>
    </script>
    <script type="text/html" id="avatar">
        @{{# if(d.headimgurl){ }}
        <img src="@{{ d.headimgurl }}" width="25" height="25">
        @{{# } }}
    </script>
    <script type="text/html" id="tags">
        @{{# if(d.tags){ }}
        @{{#  layui.each(d.tags, function(index, item){ }}
        <button class="layui-btn layui-btn-xs layui-btn-normal">@{{ item.name }}</button>
        @{{#  }); }}
        @{{# } }}
    </script>
    <script type="text/html" id="options">
        <button type="button" class="layui-btn layui-btn-xs" lay-event="tags">设置标签</button>
        <button type="button" class="layui-btn layui-btn-xs" lay-event="remark">备注</button>
    </script>
    <div class="layui-hide" id="tags-group">
        <form class="layui-form">
            @foreach($tags as $mTagId => $name)
                <input type="checkbox" name="tags[{{$mTagId}}]" value="{{$mTagId}}" title="{{$name}}">
            @endforeach
        </form>
    </div>

    <div class="layui-hide" id="fan-remark">
        <form class="layui-form">
            <textarea placeholder="请输入内容" style="width: 300px" name="remark" class="layui-textarea"></textarea>
        </form>
    </div>

    <div class="layui-hide" id="fan-detail">
        <div class="layui-text"><img width="50" height="50" src="@{{ d.headimgurl }}" alt=""></div>
        <div class="layui-text">昵称：@{{ d.nickname }}</div>
        <div class="layui-text">openid：@{{ d.openid }}</div>
        <div class="layui-text">unionid：@{{ d.unionid }}</div>
        <div class="layui-text">性别：@{{ d.sex_text }}</div>
        <div class="layui-text">国家：@{{ d.country }}</div>
        <div class="layui-text">省份：@{{ d.province }}</div>
        <div class="layui-text">城市：@{{ d.city }}</div>
        <div class="layui-text">最近活跃时间：@{{ d.last_time }}</div>
        <div class="layui-text">关注时间：@{{ d.subscribe_time }}</div>
        <div class="layui-text">取消关注时间：@{{ d.unsubscribe_time }}</div>
        <div class="layui-text">渠道来源：@{{ d.subscribe_scene_text }}</div>
        <div class="layui-text">备注：@{{ d.remark }}</div>
        <div class="layui-text">标签：
            @{{# layui.each(d.tags, function(index, item){ }}
            <button class="layui-btn layui-btn-xs layui-btn-normal">@{{ item.name }}</button>
            @{{# }); }}
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form', 'table', 'jquery', 'layer', 'laytpl', 'laydate'], function () {
            var $ = layui.jquery,
                form = layui.form,
                layer = layui.layer,
                laytpl = layui.laytpl,
                laydate = layui.laydate,
                table = layui.table;

            laydate.render({elem: '#active-start-time', type: 'datetime'});
            laydate.render({elem: '#active-end-time', type: 'datetime'});
            laydate.render({elem: '#subscribe-start-time', type: 'datetime'});
            laydate.render({elem: '#subscribe-end-time', type: 'datetime'});

            var dataTable = table.render({
                elem: '#dataTable',
                url: '{{ route('mp.base.fans.data') }}',
                toolbar: '#toolbar',
                defaultToolbar: ['filter', 'exports', 'print'],
                cols: [[
                    {type: "checkbox", width: 50, fixed: "left"},
                    {field: 'm_fan_id', title: '序号ID', width: 80, hide: true, align: 'center'},
                    {field: 'headimgurl', align: 'center', templet: "#avatar", title: '头像', width: 65},
                    {field: 'nickname', align: 'center', minWidth: 140, title: '昵称'},
                    {field: 'openid', align: 'center', minWidth: 270, title: 'openid'},
                    {field: 'unionid', align: 'center', hide: true, title: 'unionid'},
                    {field: 'tags', align: 'left', title: '标签', minWidth: 230, templet: "#tags"},
                    {
                        field: 'is_subscribe', align: 'center', title: '关注', width: 60, templet: function (data) {
                            return data.is_subscribe === 1 ? '<span style="color:#5FB878">是</span>' : '<span style="color:#FF5722">否</span>';
                        }
                    },
                    {field: 'remark', align: 'center', minWidth: 140, title: '备注'},
                    {field: 'last_time', align: 'center', minWidth: 170, title: '最近活跃时间'},
                    {field: 'subscribe_time', align: 'center', minWidth: 170, title: '关注时间'},
                    {field: 'unsubscribe_time', align: 'center', hide: true, title: '取消关注时间'},
                    {field: 'options', align: 'center', title: '操作', minWidth: 150, templet: "#options", fixed: "right"}
                ]],
                limit: 20,
                page: true
            });

            table.on('rowDouble(dataTable)', function (obj) {
                var data = obj.data, contentHtml = '';
                var temp = $('#fan-detail').html();
                laytpl(temp).render(data, function (html) {
                    contentHtml = html;
                });

                layer.open({
                    content: contentHtml,
                    title: "【" + data.nickname + "】粉丝详情",
                    shadeClose: true,
                    btn: []
                });
            });

            //监听搜索
            form.on('submit(searchBtn)', function (data) {
                dataTable.reload({
                    where: data.field,
                    page: {curr: 1}
                });
            });

            //监听工具条
            table.on('tool(dataTable)', function (obj) {
                var data = obj.data //获得当前行数据
                    , layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'tags') {
                    $('input[type=checkbox]').removeAttr('checked');
                    $(obj.data.tags).each(function (key, item) {
                        $('input[name="tags[' + item.m_tag_id + ']"]').attr('checked', 'true');
                    })

                    layer.open({
                        content: $("#tags-group").html(),
                        title: "设置【" + data.nickname + "】粉丝标签",
                        btn: ['确认', '关闭'],
                        maxWidth: "600",
                        id: "fans-tags-set"
                        , yes: function (index, layero) {
                            layer.load(0);
                            $.ajax({
                                url: "/mp_base/fans/" + data.m_fan_id + '/update',
                                type: "put",
                                data: $('#fans-tags-set form').serialize()
                            }).then(function (res) {
                                if (res.code === 0) {
                                    dataTable.reload();
                                    layer.closeAll();
                                    layer.msg("设置成功", {icon: 1});
                                }
                            })
                        }
                        , success: function () {
                            form.render();
                        }
                    })
                } else if (layEvent === 'remark') {
                    $('form textarea').html(data.remark);
                    layer.open({
                        content: $("#fan-remark").html(),
                        title: "设置【" + data.nickname + "】粉丝备注",
                        btn: ['确认', '关闭'],
                        maxWidth: "600",
                        id: "fans-remark-set"
                        , yes: function (index, layero) {
                            layer.load(0);
                            $.ajax({
                                url: "/mp_base/fans/" + data.m_fan_id + '/update',
                                type: "put",
                                data: $('#fans-remark-set form').serialize()
                            }).then(function (res) {
                                if (res.code === 0) {
                                    dataTable.reload();
                                    layer.closeAll();
                                    layer.msg("设置成功", {icon: 1});
                                }
                            })
                        }
                        , success: function () {
                            form.render();
                        }
                    })
                }
            });

            form.on('submit(syncFans)', function () {
                var load = layer.load();
                $.ajax({
                    url: "{{ route('mp.base.info.command') }}",
                    type: 'POST',
                    data: {command: 'sync:mp:fans'},
                }).then(function (resp) {
                    layer.close(load);
                    layer.alert(resp.msg, function () {
                        dataTable.reload();
                        layer.closeAll();
                    });
                });
            });
        })
        ;
    </script>
@endsection
