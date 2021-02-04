{{ csrf_field() }}
<div class="layui-form-item">
    <label class="layui-form-label">渠道名称</label>
    <div class="layui-input-block">
        <input type="text" name="name" required lay-verify="required"
               value="{{$channelCode->name ?? ''}}"
               autocomplete="off" placeholder="输入名称" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">打标签分组</label>
    <div class="layui-input-block">
        <select name="m_tag_id" lay-filter="m_tag_id" lay-verify="required">
            <option value="0">选择标签</option>
            @foreach($tags as $id => $name)
                <option value="{{$id}}" @if(isset($channelCode->m_tag_id) && $channelCode->m_tag_id == $id) selected @endif>{{$name}}</option>
            @endforeach
        </select>
    </div>
</div>

@include('mp_common.media_tab',[
    "media_type" => ($channelCode->media_type ?? \App\Models\Mp::TEXT_MEDIA),
    "data" => $channelCode->content ?? (object)[],
    "hide_media_type" => []
])

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">保存</button>
        <a href="{{ route('mp.advanced.channel_codes') }}" class="layui-btn layui-btn-normal">返回</a>
    </div>
</div>
