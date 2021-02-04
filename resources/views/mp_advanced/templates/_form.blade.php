{{ csrf_field() }}
<div class="layui-form-item">
    <label class="layui-form-label">推送主题</label>
    <div class="layui-input-block">
        <input type="text" name="theme" value="{{$template->theme ?? ''}}"
               class="layui-input temp-input" required>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">推送对象</label>
    <div class="layui-input-block">
        <select name="push_obj" class="form-control" lay-filter="push_obj" required>
            <option value=''>选择推送对象</option>
            <option value="-2" @if(isset($template->push_obj) && $template->push_obj == '-2') selected @endif>指定用户</option>
            <option value="-1" @if(isset($template->push_obj) && $template->push_obj == '-1') selected @endif>全部推送</option>
            @foreach($tags as $id => $name)
                <option value="{{$id}}" @if(isset($template->push_obj) && $template->push_obj == $id) selected @endif>标签组:{{$name}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item push_input push_more"
     @if(!isset($template->push_obj)) style="display: none"
     @elseif(isset($template->push_obj) && $template->push_obj != '-2') style="display: none" @endif>
    <label class="layui-form-label"></label>
    <div class="layui-input-inline">
        <button type="button" class="layui-btn" lay-submit lay-filter="selectUserBtn">选择用户</button>
        <input type="hidden" name="touser_ids" value="{{$template->content_data->tousers ?? ''}}">
        <div class="layui-form-mid">已选择 <span id="push-num">0</span> 个用户</div>
    </div>

</div>

<div class="layui-form-item">
    <label class="layui-form-label">定时推送</label>
    <div class="layui-input-block">
        <input type="text" class="layui-input" value="默认立即发送" disabled="">
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">跳转方式</label>
    <div class="layui-input-block">
        <select name="jump_type" lay-filter="jump_type">
            <option value="">未选择</option>
            <option value="url" @if(isset($template->content_data->url)) selected @endif>链接
            </option>
            <option value="miniprogram"
                    @if(isset($template->content_data->miniprogram)) selected @endif>小程序
            </option>
        </select>
    </div>
</div>
<div class="layui-form-item jump-input url-input"
     style="@if(!isset($template->content_data->url)) display: none @endif">
    <label class="layui-form-label">跳转链接</label>
    <div class="layui-input-block">
        <input type="text" name="url" class="layui-input"
               value="{{$template->content_data->url ?? ''}}"
               placeholder="http://****">
    </div>
</div>
<div class="layui-form-item jump-input miniprogram-input"
     style="@if(!isset($template->content_data->miniprogram)) display: none @endif">
    <label class="layui-form-label">小程序appid</label>
    <div class="layui-input-block">
        <input type="text" name="miniprogram[appid]" class="layui-input"
               value="{{$template->content_data->miniprogram->appid ?? ''}}">
    </div>
</div>
<div class="layui-form-item jump-input miniprogram-input"
     style="@if(!isset($template->content_data->miniprogram)) display: none @endif">
    <label class="layui-form-label">页面路径</label>
    <div class="layui-input-block">
        <input type="text" name="miniprogram[pagepath]"
               class="layui-input"
               value="{{$template->content_data->miniprogram->pagepath ?? ''}}"
               placeholder="index?foo=bar">
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">选择模板</label>
    <input type="hidden" class="template_id" name="template_id"
           value="{{$template->template_id ?? ''}}"/>
    <div class="layui-input-block">
        <select name="wx_template_id" class="temp-input" lay-filter="wx_template_id" data-width="320px"
                required>
            <option value="">请选择模板</option>
            @foreach($templates as $key => $val)
                <option value="{{$key}}" @if(isset($template->template_id) && $template->template_id == $val['template_id']) selected @endif>{{$val['title']}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-row">
        <div class="layui-col-sm4">
            <div class="layui-form-item">
                <label class="layui-form-label">模板预览</label>
                <textarea id="template-demo" class="layui-input-inline layui-textarea" rows="10"
                          placeholder=""
                          disabled>{!! $template->template_demo['content'] ?? '' !!}</textarea>
            </div>
        </div>
        <div class="layui-col-sm8">
            <div class="layui-form-item">
                <div id="temp-content">
                    @if(isset($template->content_data->data))
                        @foreach($template->content_data->data as $key => $item)
                            <div class="layui-inline">
                                <label class="layui-form-label"
                                       style="width: 150px">@{{@php echo($key) @endphp.DATA}}</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="content[{{$key}}][value]"
                                           value="{{$item->value ?? ''}}" class="layui-input" required>
                                </div>
                                <div class="layui-input-inline" style="width: 120px;">
                                    <input type="text" name="content[{{$key}}][color]" value="{{$item->color ?? '#000000'}}" class="layui-input"
                                           id="color-input-{{$key}}" required>
                                </div>
                                <div class="layui-inline" style="left: -11px;">
                                    <div id="color-select-{{$key}}"></div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="submit">保存</button>
        <a href="javascript:history.go(-1)" class="layui-btn layui-btn-normal">返回</a>
    </div>
</div>
