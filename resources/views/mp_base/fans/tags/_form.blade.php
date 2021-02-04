{{ csrf_field() }}
<div class="layui-form-item">
    <label class="layui-form-label">标签名称：</label>
    <div class="layui-input-inline">
        <input type="text" name="name" class="layui-input" lay-filter="name" value="{{$tag->name ?? ''}}">
    </div>
</div>
<div class="layui-form-item layui-form-text">
    <label class="layui-form-label">备注：</label>
    <div class="layui-input-block">
        <textarea placeholder="请输入内容" class="layui-textarea" lay-filter="description" name="description">{{$tag->description ?? ''}}</textarea>
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="submit">保存</button>
        <a href="javascript:history.go(-1)" class="layui-btn layui-btn-normal">返回</a>
    </div>
</div>
