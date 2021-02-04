<script id="main_menu_template" type="text/x-handlebars-template" style="display: none">
    <div class="main-menu layui-form-item" data-menu-key="@{{ menu_key }}">
        <div class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">主菜单</label>
                <div class="layui-input-inline">
                    <input type="text" name="main_menu[@{{ menu_key }}][name]" placeholder="菜单名称" autocomplete="off"
                           class="layui-input">
                </div>

                <label class="layui-form-label">有无子菜单</label>
                <div class="layui-input-inline" style="width: 60px;">
                    <select name="main_menu[@{{ menu_key }}][is_sub]" lay-filter="is_sub">
                        <option value="1">有</option>
                        <option value="0" selected>无</option>
                    </select>
                </div>
                <div class="layui-input-inline main-content" style="width: 130px;">
                    <select data-menu-key="@{{ menu_key }}"
                            name="main_menu[@{{ menu_key }}][menu_type]"
                            lay-filter="selectMenuType">
                        <option value="" selected>选择跳转类型</option>
                        <option value="{{ \App\Models\MpMenu::KEYWORD_MENU }}">事件</option>
                        <option value="{{ \App\Models\MpMenu::LINK_MENU }}">链接</option>
                        <option value="{{ \App\Models\MpMenu::MINI_APP_MENU }}">小程序</option>
                    </select>
                </div>
                <div class="layui-input-inline add-sub-menu-btn" style="width: 130px;display: none;">
                    <button data-menu-key="@{{ menu_key }}" type="button" class="layui-btn" lay-filter="addSubMenu" lay-submit>
                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i> 增加子菜单
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="sub-menu layui-form-item"></div>
</script>
