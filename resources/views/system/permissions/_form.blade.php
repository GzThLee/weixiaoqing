{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">父级</label>
    <div class="layui-input-inline">
        <select name="pid">
            <option value="0">顶级权限</option>
            @forelse($permissions as $p1)
                <option
                    value="{{$p1->id}}" {{ isset($permission->id) && $p1->id == $permission->pid ? 'selected' : '' }} >{{$p1->display_name}}</option>
                @if($p1->childs->isNotEmpty())
                    @foreach($p1->childs as $p2)
                        <option
                            value="{{$p2->id}}" {{ isset($permission->id) && $p2->id == $permission->pid ? 'selected' : '' }} >
                            &nbsp;&nbsp;&nbsp;┗━━{{$p2->display_name}}</option>
                        @if($p2->childs->isNotEmpty())
                            @foreach($p2->childs as $p3)
                                <option
                                    value="{{$p3->id}}" {{ isset($permission->id) && $p3->id == $permission->pid ? 'selected' : '' }} >
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━━{{$p3->display_name}}</option>
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @empty
            @endforelse
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-inline">
        <input type="text" name="name" value="{{$permission->name??old('name')}}" lay-verify="required"
               class="layui-input" placeholder="如：system.index">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">显示名称</label>
    <div class="layui-input-inline">
        <input type="text" name="display_name" value="{{$permission->display_name??old('display_name')}}"
               lay-verify="required" class="layui-input" placeholder="如：系统管理">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">路由</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="route_name" value="{{$permission->route_name??old('route_name')}}"
               placeholder="如：member">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">类型</label>
    <div class="layui-input-inline">
        <input type="radio" name="type" value="1" title="按钮"
               @if(isset($permission) && $permission->type==1) checked @endif >
        <input type="radio" name="type" value="2" title="菜单"
               @if(!isset($permission) || (isset($permission) && $permission->type==2)) checked @endif>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">图标</label>
    <div class="layui-input-inline">
        <input type="text" name="icon" id="iconPicker" lay-filter="iconPicker" class="layui-hide"
               value="{{$permission->icon??''}}">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">排序</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="sort" value="{{$permission->sort??0}}" placeholder="">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="">确 认</button>
        <a href="{{route('system.permissions')}}" class="layui-btn">返 回</a>
    </div>
</div>

