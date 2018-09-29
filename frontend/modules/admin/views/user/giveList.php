<?= $html ?>

<script>
$(function () {
    $(".list-container").on('click', '.giveBtn', function () {
        var $this = $(this);
        $.prompt('请输入赠送的金额', function (value) {
            $.post($this.attr('href'), {amount: value}, function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '赠送成功', function () {
                        location.reload();
                    });
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        });
        return false;
    });
});
</script>