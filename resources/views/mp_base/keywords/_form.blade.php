{{ csrf_field() }}
<div class="layui-form-item">
    <label class="layui-form-label">触发关键词</label>
    <div class="layui-input-block">
        <input type="text" name="keyword" required lay-verify="required"
               value="{{$keyword->keyword ?? old('keyword','')}}"
               autocomplete="off" placeholder="请输入标题" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">匹配规则</label>
    <div class="layui-input-block">
        <input type="radio" name="rule_type" value="1" title="精准匹配" lay-verify="required"
               @if(old('rule_type',0) ==  \App\Models\MpKeyword::EXACT) checked
               @elseif(isset($keyword->rule_type) && $keyword->rule_type == \App\Models\MpKeyword::EXACT) checked
            @endif
        />
        <input type="radio" name="rule_type" value="2" title="模糊匹配" lay-verify="required"
               @if(old('rule_type',0) ==  \App\Models\MpKeyword::FUZZY) checked
               @elseif(isset($keyword->rule_type) && $keyword->rule_type == \App\Models\MpKeyword::FUZZY) checked
            @endif
        />
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">打标签分组</label>
    <div class="layui-input-block">
        <select name="m_tag_id" lay-filter="m_tag_id" lay-verify="required">
            <option value="0">选择标签</option>
            @foreach($tags as $id => $name)
                <option value="{{$id}}"
                        @if(old('m_tag_id',0) ==  $id) selected
                        @elseif(isset($keyword->m_tag_id) && $keyword->m_tag_id == $id) selected @endif>{{$name}}</option>
            @endforeach
        </select>
    </div>
</div>
@include('mp_common.media_tab',[
    "media_type" => ($keyword->media_type ?? \App\Models\Mp::TEXT_MEDIA),
    "data" => $keyword->content ?? (object)[],
    "hide_media_type" => [\App\Models\Mp::MORE_NEWS_MEDIA]
])

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="submit">保存</button>
        <a href="{{ route('mp.base.keywords') }}" class="layui-btn layui-btn-normal">返回</a>
    </div>
</div>
