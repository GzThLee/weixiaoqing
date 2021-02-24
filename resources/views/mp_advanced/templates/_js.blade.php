<script id="temp-item-template" type="text/x-handlebars-template">
    <div class="layui-inline">
        <label class="layui-form-label" style="width: 150px">@{{name}}</label>
        <div class="layui-input-inline">
            <input type="text" name="content[@{{key}}][value]" value="" class="layui-input" required>
        </div>
        <div class="layui-input-inline" style="width: 120px;">
            <input type="text" name="content[@{{key}}][color]" value="#000000" placeholder="请选择颜色" class="layui-input"
                   id="color-input-@{{key}}" required>
        </div>
        <div class="layui-inline" style="left: -11px;">
            <div id="color-select-@{{key}}"></div>
        </div>
    </div>
</script>
<script>
    var fansIds = [];
    var temp_list = {!! json_encode($templates,JSON_UNESCAPED_UNICODE) !!};
    layui.use(['form', 'table', 'jquery', 'colorpicker', 'layer'], function () {
        var $ = layui.jquery,
            colorpicker = layui.colorpicker,
            layer = layui.layer,
            table = layui.table,
            form = layui.form;

        @if(isset($template->content_data->data))
        @foreach($template->content_data->data as $key => $item)
        colorpicker.render({
            elem: '#color-select-{{$key}}'
            , color: '{{$item->color}}'
            , done: function (color) {
                $('#color-input-{{$key}}').val(color);
            }
        });
        @endforeach
        @endif

        form.on('submit(selectUserBtn)', function (data) {
            layer.open({
                type: 2
                , title: '请选择用户'
                , area: ['800px', '400px']
                , offset: 'auto'
                , maxmin: true
                , id: 'select-user-panel'
                , content: '{{ route('mp.advanced.templates.users') }}?fans_ids=' + fansIds
                , btn: ['确认', '取消']
                , btnAlign: 'r'
                , shade: 0
                , yes: function (index, layero) {
                    var body = layer.getChildFrame('', index);
                    fansIds = body.prevObject.prevObject[0].contentWindow.fansIds;
                    $('#push-num').html(fansIds.length);
                    $('input[name="touser_ids"]').val(fansIds.join(','));
                    layer.close(index);
                }
                , btn2: function () {
                    layer.closeAll();
                }
            });
        });

        form.on('select(jump_type)', function (data) {
            $('.jump-input').hide();
            if (data.value === 'url') {
                $('.url-input').show();
            } else if (data.value === 'miniprogram') {
                $('.miniprogram-input').show();
            }
        });

        form.on('select(push_obj)', function (data) {
            $('.push_input').hide();
            if (data.value === '-2') {
                $('.push_more').show();
            }
        });

        form.on('select(wx_template_id)', function (data) {
            $('input[name="template_id"]').val('');
            if (data.value !== '') {
                var key = data.value;
                $('#template-demo').val(temp_list[key].content);
                $('input[name="template_id"]').val(temp_list[key].template_id);

                $('#temp-content').html('');
                var str = temp_list[key].content;
                var match_str_array = str.match(/@{{([\S ]*)}}/g);
                var source = document.getElementById("temp-item-template").innerHTML;
                var template = Handlebars.compile(source);
                $.each(match_str_array, function (index, item) {
                    var value = item.replace(/@{{([^"]*).DATA}}/, "$1");
                    var context = {key: value, name: '@{{' + value + '.DATA}}'};
                    var html = template(context);
                    $('#temp-content').append(html);

                    colorpicker.render({
                        elem: '#color-select-' + value
                        , color: '#000000'
                        , done: function (color) {
                            $('#color-input-' + value).val(color);
                        }
                    });
                });
            } else {
                $('#template-demo').val('');
                $('#temp-content').html('');
            }
        });
    });
</script>
