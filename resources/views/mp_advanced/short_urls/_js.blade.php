<script>
    layui.use(['layer', 'jquery', 'table', 'form', 'element', 'upload', 'laydate'], function () {
        var $ = layui.jquery
            , layer = layui.layer
            , element = layui.element
            , laydate = layui.laydate
            , upload = layui.upload;

        //执行一个laydate实例
        laydate.render({
            elem: '#end-time' //指定元素
            , type: 'datetime'
        });

        //普通图片
        upload.render({
            elem: '#image-btn'
            , url: '{{ route('system.upload.image') }}' //改成您自己的上传接口
            , before: function (obj) {
                layer.load();
            }
            , done: function (res) {
                layer.closeAll();
                layer.msg(res.msg);
                if (res.code === 0) {
                    $('#image-image').attr('src', res.data.image_url);
                    $('#image-input').val(res.data.image_url);
                }
            }
        });
        //图文
        upload.render({
            elem: '#link-btn'
            , url: '{{ route('system.upload.image') }}' //改成您自己的上传接口
            , before: function (obj) {
                layer.load();
            }
            , done: function (res) {
                layer.closeAll();
                layer.msg(res.msg);
                if (res.code === 0) {
                    $('#link-image').attr('src', res.data.image_url);
                    $('#link-input').val(res.data.image_url);
                }
            }
        });
        //小程序
        upload.render({
            elem: '#miniprogram-btn'
            , url: '{{ route('system.upload.image') }}' //改成您自己的上传接口
            , before: function (obj) {
                layer.load();
            }
            , done: function (res) {
                layer.closeAll();
                layer.msg(res.msg);
                if (res.code === 0) {
                    $('#miniprogram-image').attr('src', res.data.image_url);
                    $('#miniprogram-input').val(res.data.image_url);
                }
            }
        });


        element.on('tab(type)', function (elem) {
            $('#welcome-type').val(elem.index);
        });
    })
</script>



