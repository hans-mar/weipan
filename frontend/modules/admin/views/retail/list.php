<?= $html ?>

<script>
$(function () {
    // 查看照片
    $(".list-container").on('click', '.viewFace', function () {
        $(this).parent().find('.img-fancybox:eq(0)').trigger('click');
    });

    $(".list-container").on('click', '.editBtn', function () {
        var $this = $(this);
        $.prompt('请输入修改的返点', function (value) {
            $.post($this.attr('href'), {point: value}, function (msg) {
                if (msg.state) {
                    // $.alert(msg.info || '修改成功');
                    location.replace(location.href);
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        });
        return false;
    });
});
</script>