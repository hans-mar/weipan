<?= $html ?>

<script>
$(function () {
    'fancybox'.config({
        'minWidth': 1000
    });
    // 一键上下架
    ;!function () {
        // 更改一键下架按钮颜色
        $(".summary .l .extra-btn:eq(1)").addClass('btn-danger-outline').removeClass('btn-success-outline');
        // 点击事件
        $(".summary .l .extra-btn:eq(0)").click(function () {
            $.post($(this).attr('href'), function () {
                $.alert('一键上架成功', function () {
                    location.reload();
                });
            });
            return false;
        });
        $(".summary .l .extra-btn:eq(1)").click(function () {
            $.post($(this).attr('href'), function () {
                $.alert('一键下架成功', function () {
                    location.reload();
                });
            });
            return false;
        });
        $(".list-container").on('click', '.deleteBtn', function () {
        var $this = $(this);
        $.get($(this).attr('href'), function (msg) {
            if (msg.state) {
                $.alert(msg.info, function () {
                    location.reload();
                });
            } else {
                $.alert(msg.info);
            }
        });
        return false;
    })
    }();
});
</script>