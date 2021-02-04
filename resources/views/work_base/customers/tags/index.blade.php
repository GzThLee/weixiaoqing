@extends('layouts')

@section('content')
    <fieldset class="layui-elem-field">
        <legend>企业客户标签</legend>
        <div class="layui-field-box">企业标签由企业统一配置，配置后，企业成员可以对客户打企业标签</div>
    </fieldset>

    <div style="padding: 5px; background-color: #F2F2F2;">
        <div style="margin-bottom: 5px;background-color: white;padding: 5px;">
            <a href="{{ route('work.base.customers') }}" class="layui-btn layui-btn-sm layui-btn-warm">返回</a>
            <button type="button" lay-submit lay-filter="addTagGroup" class="layui-btn layui-btn-sm">添加标签组</button>
            <button type="button" lay-submit lay-filter="syncTagGroup" class="layui-btn layui-btn-sm layui-btn-normal">同步标签</button>
        </div>
        @foreach($tagGroups as $tagGroup)
            <form class="layui-card layui-form">
                <input type="hidden" name="w_tagg_id" value="{{$tagGroup->w_tagg_id}}">
                <input type="hidden" name="group_name" value="{{$tagGroup->name}}">
                <div class="layui-card-header">{{$tagGroup->name}}</div>
                <div class="layui-card-body">
                    <div class="layui-btn-container">
                        @foreach($tagGroup->tags as $tag)
                            <input type="hidden" lay-filter="w_tag_id[]" name="w_tag_id[]" value="{{$tag->w_tag_id}}">
                            <input type="hidden" lay-filter="tag_name[]" name="tag_name[]" value="{{$tag->name}}">
                            <button class="layui-btn layui-btn-primary layui-btn-sm">{{$tag->name}}</button>
                        @endforeach
                        <input type="hidden" lay-filter="tag_num" name="tag_num" value="{{$tagGroup->tags->count()}}">
                        <button type="button" lay-submit lay-filter="edit"
                                class="layui-btn layui-btn-normal layui-btn-sm">编辑
                        </button>
                    </div>
                </div>
            </form>
        @endforeach
    </div>
@endsection

@section('script')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script id="tag-temp" type="text/html">
        <div class="layui-form-item tag-item">
            <label class="layui-form-label">标签@{{ d.tag_num }}</label>
            <div class="layui-input-inline">
                <input type="text" name="tag_name[]" lay-filter="tag_name[]" value="" autocomplete="off"
                       class="layui-input" lay-verify="required">
            </div>
            <div class="layui-form-mid">
                <span onclick="deleteTag(this)" style="cursor:pointer"><i class="layui-icon">&#x1006;</i></span>
            </div>
        </div>
    </script>
    <script id="temp" type="text/html">
        <div style="padding: 0 15px;margin-top: 15px;">
            <form action="POST" class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">标签组名称</label>
                    <div class="layui-input-block">
                        <input type="text" lay-filter="group_name" name="group_name" value="@{{ d.group_name || '' }}"
                               autocomplete="off" class="layui-input" lay-verify="required">
                        <input type="hidden" lay-filter="w_tagg_id" name="w_tagg_id" value="@{{ d.w_tagg_id || '' }}">
                    </div>
                </div>
                <hr>
                <div id="tag-list" style="max-height: 300px;overflow: scroll;">
                    @{{# for(var k = 0;k < d.tag_num; k++){ }}
                    <div class="layui-form-item tag-item">
                        <label class="layui-form-label">标签@{{ k + 1 }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="tag_name[]" lay-filter="tag_name[]" lay-verify="required"
                                   value="@{{ d['tag_name[' + k + ']'] || '' }}" autocomplete="off" class="layui-input">
                            <input type="hidden" name="w_tag_id[]" lay-filter="w_tag_id[]"
                                   value="@{{ d['w_tag_id[' + k + ']'] || '' }}">
                        </div>
                        @{{# if(k !== 0){ }}
                        <div class="layui-form-mid">
                            <span onclick="deleteTag(this)" style="cursor:pointer"><i
                                    class="layui-icon">&#x1006;</i></span>
                        </div>
                        @{{# } }}
                    </div>
                    @{{# } }}
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <span onclick="addTag()" style="cursor:pointer"><i class="layui-icon">&#xe624;</i> 添加标签</span>
                    </div>
                </div>
                <div class="layui-form-item layui-row">
                    @{{# if(d.type === 'edit'){ }}
                    <div class="layui-col-xs6">
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" lay-filter="delete" lay-submit>删除标签组
                        </button>
                    </div>
                    <div class="layui-col-xs6" style="text-align: right">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" lay-filter="submit"
                                lay-submit>确认
                        </button>
                        <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" onclick="closeLayer()">取消
                        </button>
                    </div>
                    @{{# }else{ }}
                    <div class="layui-col-xs12" style="text-align: right">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" lay-filter="submit"
                                lay-submit>确认
                        </button>
                        <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" onclick="closeLayer()">取消
                        </button>
                    </div>
                    @{{# } }}
                </div>
            </form>
        </div>
    </script>
    <script>
        layui.use(['jquery', 'layer', 'table', 'form', 'laytpl'], function () {
            var $ = layui.jquery
                , layer = layui.layer
                , laytpl = layui.laytpl
                , form = layui.form;

            form.on('submit(edit)', function (data) {
                var temp = $('#temp').html(), tempHtml = '';
                data.field['type'] = 'edit';
                laytpl(temp).render(data.field, function (html) {
                    tempHtml = html;
                });
                 layer.open({
                    type: 1,
                    title: '编辑标签组',
                    offset: 't',
                    area: ['400px', 'auto'],
                    shadeClose: true, //点击遮罩关闭
                    content: tempHtml
                });
            });

            form.on('submit(delete)', function (data) {
                layer.confirm('删除后，已添加到客户信息的标签也一起删除，真的要删除分组吗？', {
                    title: '提醒',
                    btn: ['确认','取消']
                }, function(){
                    layer.load();
                    $.ajax({
                        url: "{{ route('work.base.customer.tags.delete') }}",
                        type: "DELETE",
                        data: data.field
                    }).then(function (res) {
                        layer.closeAll();
                        if (res.code === 0) {
                            layer.alert(res.msg,function (){
                                location.reload();
                            });
                        }else{
                            layer.alert(res.msg);
                        }
                    })
                });
            });

            form.on('submit(addTagGroup)', function () {
                var temp = $('#temp').html(), tempHtml = '';
                laytpl(temp).render({tag_num:1,type:'add'}, function (html) {
                    tempHtml = html;
                });
                layer.open({
                    type: 1,
                    title: '添加标签组',
                    offset: 't',
                    area: ['400px', 'auto'],
                    shadeClose: true, //点击遮罩关闭
                    content: tempHtml
                });
            });

            form.on('submit(syncTagGroup)', function () {
                var load = layer.load();
                $.ajax({
                    url: "{{ route('work.base.info.command') }}",
                    type: 'POST',
                    data: {command:'sync:work:tags'},
                }).then(function (resp) {
                    layer.close(load);
                    layer.alert(resp.msg,function () {
                        location.reload();
                    });
                });
                return false;
            });

            form.on('submit(submit)', function (data) {
                layer.load();
                $.ajax({
                    url: "{{ route('work.base.customer.tags.update') }}",
                    type: "PUT",
                    data: data.field
                }).then(function (res) {
                    layer.closeAll();
                    if (res.code === 0) {
                        layer.alert(res.msg,function (){
                            location.reload();
                        });
                    }else{
                        layer.alert(res.msg);
                    }
                })
            });
        })

        function deleteTag(e) {
            $(e).parent().parent().remove();
        }

        function closeLayer(e) {
            layui.layer.closeAll();
        }

        function addTag() {
            var tagTemp = $('#tag-temp').html(), tagTempHtml = '', tag_num = $('.tag-item').length;
            layui.laytpl(tagTemp).render({tag_num: tag_num + 1}, function (html) {
                tagTempHtml = html;
            });
            $('#tag-list').append(tagTempHtml);
        }
    </script>
@endsection



