@extends('layouts')
@section('style')
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css')}}" media="all">
@endsection
@section('content')
    @if(session('warning_tip'))
        @include('mp_common.warning_tip')
    @endif
    <form class="layui-form" action="{{ route('mp.base.menus.update') }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="box-main">
            @foreach($menu as $key => $item)
                <div class="main-menu layui-form-item" data-menu-key="{{ $key }}">
                    <div class="layui-form">
                        <div class="layui-form-item">
                            <label class="layui-form-label">主菜单</label>
                            <div class="layui-input-inline">
                                <input type="text" name="main_menu[{{ $key }}][name]" placeholder="菜单名称"
                                       autocomplete="off"
                                       class="layui-input"
                                       value="{{ $item['name'] }}"
                                >
                            </div>

                            <label class="layui-form-label">子菜单</label>
                            <div class="layui-input-inline" style="width: 60px;">
                                <select name="main_menu[{{ $key }}][is_sub]" lay-filter="is_sub">
                                    <option value="1" @if($item['is_sub']== 1) selected @endif>有</option>
                                    <option value="0" @if($item['is_sub']== 0) selected @endif>无</option>
                                </select>
                            </div>
                            <div class="layui-input-inline main-content"
                                 style="width: 100px; @if($item['is_sub']== 1) display:none @endif">
                                <select data-menu-key="{{ $key }}"
                                        name="main_menu[{{ $key }}][menu_type]"
                                        lay-filter="selectMenuType">
                                    <option value="" selected>跳转类型</option>
                                    <option value="{{ \App\Models\MpMenu::KEYWORD_MENU }}"
                                            @if($item['menu_type'] == \App\Models\MpMenu::KEYWORD_MENU) selected @endif>事件</option>
                                    <option value="{{ \App\Models\MpMenu::LINK_MENU }}"
                                            @if($item['menu_type'] == \App\Models\MpMenu::LINK_MENU) selected @endif>链接</option>
                                    <option value="{{ \App\Models\MpMenu::MINI_APP_MENU }}"
                                            @if($item['menu_type'] == \App\Models\MpMenu::MINI_APP_MENU) selected @endif>小程序</option>
                                </select>
                            </div>
                            <div class="layui-input-inline add-sub-menu-btn"
                                 style="width: 130px; @if($item['is_sub']== 0) display:none @endif">
                                <button data-menu-key="{{ $key }}" type="button" class="layui-btn"
                                        lay-filter="addSubMenu"
                                        lay-submit>
                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i> 增加子菜单
                                </button>
                            </div>
                            @if($item['menu_type'] == \App\Models\MpMenu::KEYWORD_MENU)
                                <div class="layui-input-inline menu-type-input">
                                    <input type="text" name="main_menu[{{ $key }}][keyword]"
                                           placeholder="事件关键字" autocomplete="off" class="layui-input"
                                           value="{{ $item['content']->key ?? '' }}"
                                    >
                                </div>
                            @elseif($item['menu_type'] == \App\Models\MpMenu::LINK_MENU)
                                <div class="layui-input-inline menu-type-input">
                                    <input type="text" name="main_menu[{{ $key }}][url]"
                                           placeholder="链接URL" autocomplete="off"
                                           class="layui-input"
                                           value="{{ $item['content']->url ?? '' }}"
                                    >
                                </div>
                            @elseif($item['menu_type'] == \App\Models\MpMenu::MINI_APP_MENU)
                                <div class="layui-input-inline menu-type-input">
                                    <input type="text" name="main_menu[{{ $key }}][appid]"
                                           class="layui-input" placeholder="小程序AppId"
                                           value="{{ $item['content']->appid ?? '' }}"
                                    >
                                </div>
                                <div class="layui-input-inline menu-type-input">
                                    <input type="text" name="main_menu[{{ $key }}][pagepath]"
                                           class="layui-input" placeholder="页面路径"
                                           value="{{ $item['content']->pagepath ?? '' }}"
                                    >
                                </div>
                                <div class="layui-input-inline menu-type-input">
                                    <input type="text" name="main_menu[{{ $key }}][url]"
                                           class="layui-input" placeholder="低版本URL"
                                           value="{{ $item['content']->url ?? '' }}"
                                    >
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="sub-menu layui-form-item">
                    @foreach($item['sub_menu'] as $sub_key => $sub)
                        <div class="layui-form">
                            <div class="layui-form-item sub-menu-item">
                                <label class="layui-form-label">﹂</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="sub_menu[{{ $key }}][{{ $sub_key }}][name]"
                                           placeholder="菜单名称" autocomplete="off"
                                           class="layui-input"
                                           value="{{ $sub->name ?? '' }}"
                                    >
                                </div>
                                <div class="layui-input-inline" style="width: 100px;">
                                    <select data-menu-key="{{ $key }}"
                                            data-sub-key="{{ $sub_key }}"
                                            name="sub_menu[{{ $key }}][{{ $sub_key }}][menu_type]"
                                            lay-filter="subSelectMenuType">
                                        <option value="" selected>跳转类型</option>
                                        <option value="{{ \App\Models\MpMenu::KEYWORD_MENU }}"
                                                @if($sub['menu_type'] == \App\Models\MpMenu::KEYWORD_MENU) selected @endif
                                        >事件</option>
                                        <option value="{{ \App\Models\MpMenu::LINK_MENU }}"
                                                @if($sub['menu_type'] == \App\Models\MpMenu::LINK_MENU) selected @endif
                                        >链接</option>
                                        <option value="{{ \App\Models\MpMenu::MINI_APP_MENU }}"
                                                @if($sub['menu_type'] == \App\Models\MpMenu::MINI_APP_MENU) selected @endif
                                        >小程序</option>
                                    </select>
                                </div>
                                <button type='button' class="layui-btn layui-btn-danger remove-sub-menu">删除</button>

                                @if($sub['menu_type'] == \App\Models\MpMenu::KEYWORD_MENU)
                                    <div class="layui-input-inline menu-type-input">
                                        <input type="text" name="sub_menu[{{ $key }}][{{ $sub_key }}][keyword]"
                                               placeholder="事件关键字" autocomplete="off" class="layui-input"
                                               value="{{ $sub['content']->key ?? '' }}"
                                        >
                                    </div>
                                @elseif($sub['menu_type'] == \App\Models\MpMenu::LINK_MENU)
                                    <div class="layui-input-inline menu-type-input">
                                        <input type="text" name="sub_menu[{{ $key }}][{{ $sub_key }}][url]"
                                               placeholder="链接URL" autocomplete="off" class="layui-input"
                                               value="{{ $sub['content']->url ?? '' }}">
                                    </div>
                                @elseif($sub['menu_type'] == \App\Models\MpMenu::MINI_APP_MENU)
                                    <div class="layui-input-inline menu-type-input">
                                        <input type="text" name="sub_menu[{{ $key }}][{{ $sub_key }}][appid]"
                                               class="layui-input" placeholder="小程序AppId"
                                               value="{{ $sub['content']->appid ?? '' }}"
                                        >
                                    </div>
                                    <div class="layui-input-inline menu-type-input">
                                        <input type="text" name="sub_menu[{{ $key }}][{{ $sub_key }}][pagepath]"
                                               class="layui-input" placeholder="页面路径"
                                               value="{{ $sub['content']->pagepath ?? '' }}"
                                        >
                                    </div>
                                    <div class="layui-input-inline menu-type-input">
                                        <input type="text" name="sub_menu[{{ $key }}][{{ $sub_key }}][url]"
                                               class="layui-input" placeholder="低版本URL"
                                               value="{{ $sub['content']->url ?? '' }}"
                                        >
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="layui-form layui-form-pane">
            <div id="add-main-menu-btn" class="layui-form-item @if (count($menu) >= 3) layui-hide @endif">
                <div class="layui-input-block">
                    <button type="button" class="layui-btn layui-btn-normal">
                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i> 增加主菜单
                    </button>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="layui-btn">保存修改</button>
                    <button type="button" lay-submit lay-filter="syncMenu" class="layui-btn layui-btn-normal">同步菜单</button>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script type="application/javascript" src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script type="application/javascript" src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    @include('mp_base.menu.main_temp')
    @include('mp_base.menu.sub_temp')
    <script id="menu-type-keyword-temp" type="text/x-handlebars-template">
        <div class="layui-input-inline menu-type-input">
            <input type="text" name="@{{ name }}[@{{ key }}][keyword]" placeholder="事件关键字" autocomplete="off"
                   class="layui-input">
        </div>
    </script>
    <script id="menu-type-link-temp" type="text/x-handlebars-template">
        <div class="layui-input-inline menu-type-input">
            <input type="text" name="@{{ name }}[@{{ key }}][url]" placeholder="链接URL" autocomplete="off"
                   class="layui-input">
        </div>
    </script>
    <script id="menu-type-miniapp-temp" type="text/x-handlebars-template">
        <div class="layui-input-inline menu-type-input">
            <input type="text" name="@{{ name }}[@{{ key }}][appid]" class="layui-input" placeholder="小程序AppId">
        </div>
        <div class="layui-input-inline menu-type-input">
            <input type="text" name="@{{ name }}[@{{ key }}][pagepath]" class="layui-input" placeholder="页面路径">
        </div>
        <div class="layui-input-inline menu-type-input">
            <input type="text" name="@{{ name }}[@{{ key }}][url]" class="layui-input" placeholder="低版本URL">
        </div>
    </script>
    <script type="application/javascript">
        var adminForm = null;
        var form = null;
        var menuTypeTemplateList = {
            '{{\App\Models\MpMenu::KEYWORD_MENU}}': Handlebars.compile(document.getElementById("menu-type-keyword-temp").innerHTML),
            '{{\App\Models\MpMenu::LINK_MENU}}': Handlebars.compile(document.getElementById("menu-type-link-temp").innerHTML),
            '{{\App\Models\MpMenu::MINI_APP_MENU}}': Handlebars.compile(document.getElementById("menu-type-miniapp-temp").innerHTML)
        };
        layui.use(['form', 'layer', 'element', 'jquery'], function () {
            var form = layui.form,
                $ = layui.jquery,
                layer = layui.layer,
                element = layui.element;

            form.render();

            $(document).on('click', '.remove-sub-menu', function () {
                $(this).parent('.layui-form-item').remove();
            });

            $('#add-main-menu-btn').on('click', function () {
                var main_menu_key = $('.main-menu').length;
                var source = document.getElementById("main_menu_template").innerHTML;
                var template = Handlebars.compile(source);
                var context = {menu_key: main_menu_key};
                var html = template(context);
                $('.box-main').append(html);
                var main_menu_length = $('.main-menu').length;
                if (main_menu_length >= 3) {
                    $('#add-main-menu-btn').hide();
                }
                form.render();
            });

            //监听提交是否子菜单
            form.on('select(is_sub)', function (data) {
                var value = data.value;
                switch (parseInt(value)) {
                    case 1:
                        $(data.elem).parents('.main-menu').find('.add-sub-menu-btn').show();   // 隐藏已经填的东西
                        $(data.elem).parents('.main-menu').find('.menu-type-input').hide();
                        $(data.elem).parents('.main-menu').find('.main-content').hide();
                        $(data.elem).parent().find('.menu-type-input').remove();
                        $(data.elem).hide();
                        break;
                    case 0:
                        // 隐藏添加子菜单的按钮
                        $(data.elem).parents('.main-menu').find('.add-sub-menu-btn').hide();
                        $(data.elem).parents('.main-menu').find('.menu-type-input').show();
                        $(data.elem).parents('.main-menu').find('.main-content').show();
                        // 清空已填内容
                        $(data.elem).parents('.main-menu').next().html('');
                        break;
                }
                form.render();
                return false;
            });

            //监听主菜单选择
            form.on('select(selectMenuType)', function (data) {
                var _value = data.value;
                var menu_key = $(data.elem).data('menu-key');
                var menu_input = $(data.elem).parent().parent('.layui-form-item');
                $(menu_input).find('.menu-type-input').remove();
                menu_input.append(menuTypeTemplateList[_value]({name: 'main_menu', key: menu_key}));
                form.render();
                return false;
            });

            //监听子菜单选择
            form.on('select(subSelectMenuType)', function (data) {
                var _value = data.value;
                var menu_key = $(data.elem).data('menu-key');
                var sub_key = $(data.elem).data('sub-key');
                var menu_input = $(data.elem).parent().parent('.layui-form-item');
                $(menu_input).find('.menu-type-input').remove();
                menu_input.append(menuTypeTemplateList[_value]({name: 'sub_menu[' + menu_key + ']', key: sub_key}));
                form.render();
                return false;
            });

            //增加子菜单
            form.on('submit(addSubMenu)', function (data) {
                var _this = data.elem;
                var sub_menu = $(_this).parent().parent().parent().parent().next('.sub-menu');
                var sub_menu_item = $(_this).parent().parent().parent().parent().next('.sub-menu').find('.sub-menu-item').length;
                var menu_key = $(_this).data('menu-key');
                var source = document.getElementById("sub-menu-template").innerHTML;
                var template = Handlebars.compile(source);
                if (sub_menu_item < 5) {
                    var context = {menu_key: menu_key, sub_key: sub_menu_item};
                    var html = template(context);
                    sub_menu.append(html);
                    form.render();
                } else {
                    layer.msg('子菜单最多5个');
                }
            });

            form.on('submit(syncMenu)', function () {
                var load = layer.load();
                $.ajax({
                    url: "{{ route('mp.base.info.command') }}",
                    type: 'POST',
                    data: {command: 'sync:mp:menus'},
                }).then(function (resp) {
                    layer.close(load);
                    layer.alert(resp.msg, function () {
                        layer.closeAll();
                    });
                });
                return false;
            });
        });
    </script>
@endsection
