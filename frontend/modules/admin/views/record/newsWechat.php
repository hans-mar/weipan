<?= $html ?>

<script>
$(function () {   
    $(".list-container").on('click', '.updateState', function () {
        var $this = $(this);
        $.post($this.attr('href'), {point: value}, function (msg) {
            if (msg.state) {
                location.replace(location.href);
            } else {
                $.alert(msg.info);
            }
        }, 'json');
        return false;
    });   
});
$(function () {
    $(".list-container").on('click', '.sendMessage', function () {
        var $this = $(this);
        // $.prompt('是否推送消息', function (value) {
            $.post($this.attr('href'), function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '推送成功！');
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        // });
        return false;
    });
});
</script>