<script>
    layui.use(['form', 'layedit', 'laydate', 'element'], function () {
        var $ = layui.jquery,
            form = layui.form
            , layer = layui.layer
            , element = layui.element;

        //监听Tab切换
        element.on('tab(media-tab)', function () {
            $('#media-type').val(this.getAttribute('lay-id'));
        });
    });
</script>
