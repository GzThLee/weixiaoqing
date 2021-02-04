<script id="sub-menu-template" type="text/x-handlebars-template" style="display: none">
    <div class="layui-form">
        <div class="layui-form-item sub-menu-item">
            <label class="layui-form-label">﹂</label>
            <div class="layui-input-inline">
                <input type="text" name="sub_menu[@{{ menu_key }}][@{{ sub_key }}][name]" placeholder="菜单名称" autocomplete="off"
                       class="layui-input">
            </div>
            <div class="layui-input-inline" style="width: 100px;">
                <select data-menu-key="@{{ menu_key }}"
                        data-sub-key="@{{ sub_key }}"
                        name="sub_menu[@{{ menu_key }}][@{{ sub_key }}][menu_type]"
                        lay-filter="subSelectMenuType">
                    <option value="" selected>选择跳转类型</option>
                    <option value="{{ \App\Models\MpMenu::KEYWORD_MENU }}">事件</option>
                    <option value="{{ \App\Models\MpMenu::LINK_MENU }}">链接</option>
                    <option value="{{ \App\Models\MpMenu::MINI_APP_MENU }}">小程序</option>
                </select>
            </div>
            <button type='button' class="layui-btn layui-btn-danger remove-sub-menu">删除</button>
        </div>
    </div>
</script>
