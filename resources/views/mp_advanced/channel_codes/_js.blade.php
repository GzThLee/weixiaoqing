<script type="text/javascript">
    layui.use(['form', 'element', 'jquery'], function () {
        var element = layui.element,
            $ = layui.jquery,
            form = layui.form;

        form.render();

        //监听Tab切换
        element.on('tab(media-tab)', function () {
            $('#media-type').val(this.getAttribute('lay-id'));
        });
    });
</script>
