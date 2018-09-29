<?= $html ?>

<script>
$(function () {
    $(".list-container").on('click', '.verifyBtn', function () {
        var $this = $(this);
        $.confirm('确认' + $this.html() + '？', function () {
            $.post($this.attr('href'), function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '审核通过', function () {
                        location.reload();
                    });
                } else {
                    $.alert(msg.info);
                }
            });
        });
        return false;
    });
});
</script>