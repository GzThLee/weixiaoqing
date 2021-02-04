<script>
    layui.use(['layer','element','form','iconPickerFa'],function () {
        var $ = layui.jquery,
            layer = layui.layer,
            iconPickerFa = layui.iconPickerFa;

        //选择图标
        window.chioceIcon = function (obj) {
            var icon = $(obj).data('class');
            $("input[name='icon']").val(icon);
            $("#icon_box").html('<i class="layui-icon '+$(obj).data('class')+'"></i> '+$(obj).data('name'));
            layer.closeAll();
        };

        //弹出图标
        iconPickerFa.render({
            elem: '#iconPicker',
            url: "{{ asset('layuimini/lib/font-awesome-4.7.0/less/variables.less') }}",
            search: true,
            page: true,
            limit: 33,
            cellWidth:'7%',
            click: function (data) {
                console.log('iconPickerFa:click');
            },
            // 渲染成功后的回调
            success: function (d) {
                console.log('iconPickerFa:success');
            }
        });
    });
</script>
