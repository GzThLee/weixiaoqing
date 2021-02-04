@extends('layouts')

@section('style')
    <style>
        .cate-box{margin-bottom: 15px;padding-bottom:10px;border-bottom: 1px solid #f0f0f0}
        .cate-box dt{margin-bottom: 10px;}
        /*.cate-box dt .cate-first{padding:10px 20px}*/
        .cate-box dd{padding:0 40px}
        .cate-box dd .cate-second{margin-bottom: 10px}
        .cate-box dd .cate-third{padding:0 40px;margin-bottom: 10px}
    </style>
@endsection

@section('content')
    <fieldset class="layui-elem-field layui-field-title">
        <legend>分配角色权限</legend>
    </fieldset>

    <form action="{{route('system.roles.assignPermission',['id'=>$role->id])}}" method="post"
          class="layui-form">
        {{csrf_field()}}
        {{method_field('put')}}
        <div class="layui-form-item">
            <label for="" class="layui-form-label">名称</label>
            <div class="layui-word-aux layui-form-mid">{{ $role->name }}</div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">显示名称</label>
            <div class="layui-word-aux layui-form-mid">{{ $role->display_name }}</div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">权限</label>
            <div class="layui-input-block">
                @forelse($permissions as $p1)
                    <dl class="cate-box">
                        <dt>
                            <div class="cate-first"><input id="menu{{$p1->id}}" type="checkbox"
                                                           name="permissions[]" value="{{$p1->id}}"
                                                           title="{{$p1->display_name}}"
                                                           lay-skin="primary" {{$p1->own??''}} ></div>
                        </dt>
                        @if($p1->childs->isNotEmpty())
                            @foreach($p1->childs as $p2)
                                <dd>
                                    <div class="cate-second"><input id="menu{{$p1->id}}-{{$p2->id}}"
                                                                    type="checkbox" name="permissions[]"
                                                                    value="{{$p2->id}}"
                                                                    title="{{$p2->display_name}}"
                                                                    lay-skin="primary" {{$p2->own??''}}></div>
                                    @if($p2->childs->isNotEmpty())
                                        <div class="cate-third">
                                            @foreach($p2->childs as $p3)
                                                <input type="checkbox"
                                                       id="menu{{$p1->id}}-{{$p2->id}}-{{$p3->id}}"
                                                       name="permissions[]" value="{{$p3->id}}"
                                                       title="{{$p3->display_name}}"
                                                       lay-skin="primary" {{$p3->own??''}}>
                                            @endforeach
                                        </div>
                                    @endif
                                </dd>
                            @endforeach
                        @endif
                    </dl>
                @empty
                    <div style="text-align: center;padding:20px 0;">
                        无数据
                    </div>
                @endforelse
            </div>
        </div>

        <div class="layui-form-item">
            <label for="" class="layui-form-label"></label>
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" lay-submit="">确 认</button>
                <a href="{{route('system.roles')}}" class="layui-btn">返 回</a>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script type="text/javascript">
        layui.use(['layer', 'table', 'form'], function () {
            var $ = layui.jquery,
                layer = layui.layer
                , form = layui.form
                , table = layui.table;

            form.on('checkbox', function (data) {
                var check = data.elem.checked;//是否选中
                var checkId = data.elem.id;//当前操作的选项框
                var ids = checkId.split("-");
                if (check) {
                    //选中
                    if (ids.length === 3) {
                        //第三极菜单
                        //第三极菜单选中,则他的上级选中
                        $("#" + (ids[0] + '-' + ids[1])).prop("checked", true);
                        $("#" + (ids[0])).prop("checked", true);
                    } else if (ids.length === 2) {
                        //第二季菜单
                        $("#" + (ids[0])).prop("checked", true);
                        $("input[id*=" + ids[0] + '-' + ids[1] + "]").each(function (i, ele) {
                            $(ele).prop("checked", true);
                        });
                    } else {
                        //第一季菜单不需要做处理
                        $("input[id*=" + ids[0] + "-]").each(function (i, ele) {
                            $(ele).prop("checked", true);
                        });
                    }
                } else {
                    //取消选中
                    if (ids.length === 2) {
                        //第二极菜单
                        $("input[id*=" + ids[0] + '-' + ids[1] + "]").each(function (i, ele) {
                            $(ele).prop("checked", false);
                        });
                    } else if (ids.length === 1) {
                        $("input[id*=" + ids[0] + "-]").each(function (i, ele) {
                            $(ele).prop("checked", false);
                        });
                    }
                }
                form.render();
            });
        })
    </script>
@endsection

