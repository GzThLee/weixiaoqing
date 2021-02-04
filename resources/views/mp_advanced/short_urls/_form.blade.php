{{ csrf_field() }}
<div class="layui-form-item">
    <label class="layui-form-label">名称:</label>
    <div class="layui-input-block">
        <input type="text" name="name" lay-verify="required" value="{{ $shortUrl->name ?? old('name','') }}"
               placeholder="请输入名称" class="layui-input">
    </div>
</div>

<div class="layui-form-item layui-form-text">
    <label class="layui-form-label">长链接：</label>
    <div class="layui-input-block">
        <textarea placeholder="请输入内容" name="long_url"
                  class="layui-textarea">{{ $shortUrl->long_url ?? old('long_url','') }}</textarea>
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">保存</button>
        <a href="{{ route('mp.advanced.short_urls') }}" class="layui-btn layui-btn-normal">返回</a>
    </div>
</div>



