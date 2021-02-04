<input type="hidden" name="media_type" id="media-type" value="{{ $media_type }}">
<div class="layui-tab layui-tab-brief" lay-filter="media-tab">
    <ul class="layui-tab-title">
        @if(!in_array(\App\Models\Mp::TEXT_MEDIA,$hide_media_type))
            <li @if($media_type == \App\Models\Mp::TEXT_MEDIA) class="layui-this"
                @endif lay-id="{{ \App\Models\Mp::TEXT_MEDIA }}">文本信息
            </li>
        @endif
        @if(!in_array(\App\Models\Mp::NEWS_MEDIA,$hide_media_type))
            <li @if($media_type == \App\Models\Mp::NEWS_MEDIA) class="layui-this"
                @endif lay-id="{{ \App\Models\Mp::NEWS_MEDIA }}">单图文
            </li>
        @endif
        @if(!in_array(\App\Models\Mp::MORE_NEWS_MEDIA,$hide_media_type))
            <li @if($media_type == \App\Models\Mp::MORE_NEWS_MEDIA) class="layui-this"
                @endif lay-id="{{ \App\Models\Mp::MORE_NEWS_MEDIA }}">多图文
            </li>
        @endif
        {{--        <li lay-id="{{ \App\Models\Mp::IMAGE_MEDIA }}">图片</li>--}}
        {{--        <li lay-id="{{ \App\Models\Mp::VOICE_MEDIA }}">语音</li>--}}
        {{--        <li lay-id="{{ \App\Models\Mp::VIDEO_MEDIA }}">视频</li>--}}
        {{--        <li lay-id="{{ \App\Models\Mp::CARD_MEDIA }}">卡券</li>--}}
        {{--        <li lay-id="{{ \App\Models\Mp::MINI_APP_MEDIA }}">小程序</li>--}}
    </ul>
    <div class="layui-tab-content">
        @if(!in_array(\App\Models\Mp::TEXT_MEDIA,$hide_media_type))
            <div class="layui-tab-item @if($media_type == \App\Models\Mp::TEXT_MEDIA) layui-show @endif">
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">文本消息</label>
                    <div class="layui-input-block">
                        <textarea placeholder="请输入内容"
                                  name="content[{{ \App\Models\Mp::TEXT_MEDIA }}][content]"
                                  class="layui-textarea">{{ $data->content ?? '' }}</textarea>
                        <div class="layui-btn-group">
                            <button type="button" lay-submit lay-filter="addEmoji"
                                    class="layui-btn layui-btn-normal layui-btn-xs">emoji表情
                            </button>
                            <button type="button" lay-submit lay-filter="addUrl"
                                    class="layui-btn layui-btn-normal layui-btn-xs">URL链接
                            </button>
                            <button type="button" lay-submit lay-filter="addMini"
                                    class="layui-btn layui-btn-normal layui-btn-xs">小程序链接
                            </button>
                            <button type="button" lay-submit lay-filter="addNickname"
                                    class="layui-btn layui-btn-normal layui-btn-xs">粉丝昵称
                            </button>
                            <button type="button" lay-submit lay-filter="clearAll"
                                    class="layui-btn layui-btn-danger layui-btn-xs">清空
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(!in_array(\App\Models\Mp::NEWS_MEDIA,$hide_media_type))
            <div class="layui-tab-item @if($media_type == \App\Models\Mp::NEWS_MEDIA) layui-show @endif">
                <div class="layui-form-item">
                    <label class="layui-form-label">标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="content[{{ \App\Models\Mp::NEWS_MEDIA }}][title]" autocomplete="off"
                               placeholder="请输入标题" class="layui-input"
                               value="{{ ($media_type == \App\Models\Mp::NEWS_MEDIA) ? ($data->title ?? '') : '' }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">封面URL</label>
                    <div class="layui-input-block">
                        <input type="text" name="content[{{ \App\Models\Mp::NEWS_MEDIA }}][cover_url]"
                               autocomplete="off"
                               placeholder="请输入标题" class="layui-input"
                               value="{{ ($media_type == \App\Models\Mp::NEWS_MEDIA) ? ($data->cover_url ?? '') : '' }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">上传封面</label>
                    <div class="layui-input-block">
                        <div class="layui-upload">
                            <button type="button" class="layui-btn" id="upload-file-media">上传图片</button>
                            <div class="layui-upload-list">
                                <img class="layui-upload-img" id="upload-file-media-img" width="100" height="100"
                                     src="{{ ($media_type == \App\Models\Mp::NEWS_MEDIA) ? ($data->cover_url ?? 'https://via.placeholder.com/100X100') : 'https://via.placeholder.com/100X100' }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">描述</label>
                    <div class="layui-input-block">
                    <textarea placeholder="请输入内容" name="content[{{ \App\Models\Mp::NEWS_MEDIA }}][description]"
                              class="layui-textarea">{{ ($media_type == \App\Models\Mp::NEWS_MEDIA) ? ($data->description ?? '') : '' }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">链接URL</label>
                    <div class="layui-input-block">
                        <input type="text" name="content[{{ \App\Models\Mp::NEWS_MEDIA }}][url]" placeholder="请输入标题"
                               class="layui-input"
                               value="{{ ($media_type == \App\Models\Mp::NEWS_MEDIA) ? ($data->url ?? '') : '' }}">
                    </div>
                </div>
            </div>
        @endif
        @if(!in_array(\App\Models\Mp::MORE_NEWS_MEDIA,$hide_media_type))
            <div class="layui-tab-item @if($media_type == \App\Models\Mp::MORE_NEWS_MEDIA) layui-show @endif">
                <div class="more-news">
                    @if($media_type == \App\Models\Mp::MORE_NEWS_MEDIA)
                        @for($num = 0;$num < count($data->title);$num++)
                            <div class="layui-form-item more-news-item">
                                <fieldset class="layui-elem-field layui-field-title">
                                    <legend class="more-item-number">第{{$num + 1}}条</legend>
                                </fieldset>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">标题</label>
                                    <div class="layui-input-block">
                                        <input type="text"
                                               name="content[{{ \App\Models\Mp::MORE_NEWS_MEDIA }}][title][]"
                                               autocomplete="off" placeholder="请输入标题" class="layui-input"
                                               value="{{ $data->title[$num] ?? '' }}">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">封面URL</label>
                                    <div class="layui-input-block">
                                        <input type="text"
                                               name="content[{{ \App\Models\Mp::MORE_NEWS_MEDIA }}][cover_url][]"
                                               autocomplete="off" placeholder="请输入标题" class="layui-input"
                                               value="{{ $data->cover_url[$num] ?? '' }}"
                                               id="upload-file-medias-url-{{$num + 1}}">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">上传封面</label>
                                    <div class="layui-input-block">
                                        <div class="layui-upload">
                                            <button type="button" class="layui-btn"
                                                    id="upload-file-medias-{{$num + 1}}">上传图片
                                            </button>
                                            <div class="layui-upload-list">
                                                <img class="layui-upload-img" id="upload-file-medias-img-{{$num + 1}}"
                                                     style="max-width: 900px;max-height:300px "
                                                     src="{{ ($media_type == \App\Models\Mp::MORE_NEWS_MEDIA) ? ($data->cover_url[$num] ?? 'https://via.placeholder.com/100X100') : 'https://via.placeholder.com/100X100' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">链接URL</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="content[{{ \App\Models\Mp::MORE_NEWS_MEDIA }}][url][]"
                                               autocomplete="off" placeholder="请输入标题" class="layui-input"
                                               value="{{ $data->url[$num] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        @endfor
                    @endif
                </div>
                <div>
                    @if(isset($data->title) && count($data->title) == 8)
                        <button type="button" lay-submit lay-filter="addNews" style="display: none"
                                class="layui-btn layui-btn-normal layui-btn-fluid" id="add-news-btn">增加图文
                        </button>
                    @else
                        <button type="button" lay-submit lay-filter="addNews"
                                class="layui-btn layui-btn-normal layui-btn-fluid" id="add-news-btn">增加图文
                        </button>
                    @endif
                </div>
                <div style="margin-top: 10px;">
                    @if(isset($data->title) && count($data->title) > 1)
                        <button type="button" lay-submit lay-filter="delNews" id="del-news-btn"
                                class="layui-btn layui-btn-danger layui-btn-fluid">删除图文
                        </button>
                    @else
                        <button type="button" lay-submit lay-filter="delNews" style="display: none"
                                class="layui-btn layui-btn-danger layui-btn-fluid" id="del-news-btn">删除图文
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
<script id="more-news-template" type="text/x-handlebars-template">
    <div class="layui-form-item more-news-item">
        <fieldset class="layui-elem-field layui-field-title">
            <legend class="more-item-number">第@{{ number }}条</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label">标题</label>
            <div class="layui-input-block">
                <input type="text" name="content[{{ \App\Models\Mp::MORE_NEWS_MEDIA }}][title][@{{ key }}]"
                       autocomplete="off" placeholder="请输入标题" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">封面URL</label>
            <div class="layui-input-block">
                <input type="text" name="content[{{ \App\Models\Mp::MORE_NEWS_MEDIA }}][cover_url][@{{ key }}]"
                       autocomplete="off" placeholder="请输入标题" class="layui-input"
                       id="upload-file-medias-url-@{{number}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">上传封面</label>
            <div class="layui-input-block">
                <div class="layui-upload">
                    <button type="button" class="layui-btn" id="upload-file-medias-@{{number}}">上传图片</button>
                    <div class="layui-upload-list">
                        <img class="layui-upload-img" id="upload-file-medias-img-@{{number}}" width="100" height="100"
                             src="https://via.placeholder.com/100X100">
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">链接URL</label>
            <div class="layui-input-block">
                <input type="text" name="content[{{ \App\Models\Mp::MORE_NEWS_MEDIA }}][url][@{{ key }}]"
                       autocomplete="off" placeholder="请输入标题" class="layui-input">
            </div>
        </div>
    </div>
</script>
<script type="text/html" id="mini-app-panel">
    <div class="layui-form">
        <div class="layui-form-item" style="margin-top: 15px">
            <label class="layui-form-label">名称：</label>
            <div class="layui-input-inline">
                <input type="text" name="mini_name" class="layui-input" lay-filter="name" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">appid：</label>
            <div class="layui-input-inline">
                <input type="text" name="mini_appid" class="layui-input" lay-filter="appid" value="">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">路径：</label>
            <div class="layui-input-inline">
                <textarea style="height: 80px" name="mini_path" class="layui-input" lay-filter="path"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">链接：</label>
            <div class="layui-input-inline">
                <textarea style="height: 80px" name="mini_href" class="layui-input" lay-filter="href"></textarea>
            </div>
        </div>
    </div>
</script>
@include('mp_common.emoji_panel')
<script>
    layui.use(['upload', 'jquery', 'layer', 'form'], function () {
        var $ = layui.jquery
            , layer = layui.layer
            , form = layui.form
            , upload = layui.upload;

        var moreNewsTempSource = $('#more-news-template').html();
        var moreNewsTemp = Handlebars.compile(moreNewsTempSource);
        @if($media_type != \App\Models\Mp::MORE_NEWS_MEDIA)
        var html = moreNewsTemp({number: 1, key: 0});
        $('.more-news').append(html);
        @endif

        form.on('submit(addEmoji)', function () {
            layer.open({
                type: 1
                , title: '添加表情'
                , area: ['710px', '510px']
                , offset: 'auto'
                , id: 'add-emoji' //防止重复弹出
                , btn: ['关闭']
                , content: $('#emoji-panel').html()
                , success: function () {
                    $('li.emoji').on('click', function () {
                        var contentHtml = $('textarea[name="content[{{ \App\Models\Mp::TEXT_MEDIA }}][content]"]').val();
                        contentHtml = contentHtml + $(this).html();
                        $('textarea[name="content[{{ \App\Models\Mp::TEXT_MEDIA }}][content]"]').val(contentHtml);
                        layer.msg('插入成功', {time: 800});
                    })
                }
            });
        });

        form.on('submit(addUrl)', function () {
            layer.prompt({title: '输入链接名称'}, function (value, index, elem) {
                var urlName = value;
                layer.close(index);
                layer.prompt({title: '输入链接地址', formType: 2}, function (value, index, elem) {
                    var contentHtml = $('textarea[name="content[{{ \App\Models\Mp::TEXT_MEDIA }}][content]"]').val();
                    contentHtml = contentHtml + '<a href="' + value + '">' + urlName + '</a>';
                    $('textarea[name="content[{{ \App\Models\Mp::TEXT_MEDIA }}][content]"]').val(contentHtml);
                    layer.close(index);
                    layer.msg('插入成功', {time: 800});
                });
            });
        });

        form.on('submit(addMini)', function () {
            layer.open({
                type: 1
                , title: '插入小程序'
                , area: ['350px', '420px']
                , offset: 'auto'
                , id: 'add-mini-app' //防止重复弹出
                , btn: ['插入', '关闭']
                , content: $('#mini-app-panel').html()
                , yes: function (index) {
                    var name = $('input[name="mini_name"]').val();
                    var appid = $('input[name="mini_appid"]').val();
                    var path = $('textarea[name="mini_path"]').val();
                    var href = $('textarea[name="mini_href"]').val();
                    var contentHtml = $('textarea[name="content[{{ \App\Models\Mp::TEXT_MEDIA }}][content]"]').val();
                    contentHtml = contentHtml + '<a data-miniprogram-appid="' + appid + '" data-miniprogram-path="' + path + '" href="' + href + '" >' + name + '</a>';
                    $('textarea[name="content[{{ \App\Models\Mp::TEXT_MEDIA }}][content]"]').val(contentHtml);
                    layer.close(index);
                    layer.msg('插入成功', {time: 800});
                }
                , success: function () {
                    form.render();
                }
            });
        });

        form.on('submit(addNickname)', function () {
            var contentHtml = $('textarea[name="content[{{ \App\Models\Mp::TEXT_MEDIA }}][content]"]').val();
            contentHtml = contentHtml + '@nickname';
            $('textarea[name="content[{{ \App\Models\Mp::TEXT_MEDIA }}][content]"]').val(contentHtml);
        });

        form.on('submit(clearAll)', function () {
            $('textarea[name="content[{{ \App\Models\Mp::TEXT_MEDIA }}][content]"]').val('');
        });

        //单图片上传
        upload.render({
            elem: '#upload-file-media'
            , url: '{{ route('system.upload.image') }}' //改成您自己的上传接口
            , data: {"_token": "{{ csrf_token() }}"}
            , done: function (resp) {
                if (resp.code === 0) {
                    $('input[name="content[{{ \App\Models\Mp::NEWS_MEDIA }}][cover_url]"]').val(resp.data.image_url);
                    $('#upload-file-media-img').attr('src', resp.data.image_url);
                    return layer.msg(resp.msg);
                }
                return layer.msg(resp.msg);
            }
        });

        function reloadMedias() {
            for (var i = 1; i <= 8; i++) {
                //多图片上传
                upload.render({
                    _btnId: '#upload-file-medias-' + i,
                    _imgId: '#upload-file-medias-img-' + i,
                    _urlId: '#upload-file-medias-url-' + i,
                    elem: '#upload-file-medias-' + i,
                    url: '{{ route('system.upload.image') }}', //改成您自己的上传接口
                    data: {"_token": "{{ csrf_token() }}"},
                    done: function (resp) {
                        if (resp.code === 0) {
                            $(this._urlId).val(resp.data.image_url);
                            $(this._imgId).attr('src', resp.data.image_url);
                            layer.msg(resp.msg);
                        } else {
                            layer.msg(resp.msg);
                        }
                    }
                });
            }
        }

        form.on('submit(addNews)', function () {
            var length = $('.more-news-item').length;
            if (length < 8) {
                var html = moreNewsTemp({number: length + 1, key: length});
                $('.more-news').append(html);
                $('#del-news-btn').show();
            } else if ((length + 1) === 8) {
                $(this).hide();
            }
            reloadMedias();
        });

        form.on('submit(delNews)', function () {
            $('.more-news .more-news-item:last').remove();
            var length = $('.more-news-item').length;
            if (length === 1) {
                $(this).hide();
            }
            reloadMedias();
        });

        reloadMedias();
    });
</script>
