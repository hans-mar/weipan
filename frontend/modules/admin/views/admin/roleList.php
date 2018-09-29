<?= $html ?>

<script>
$(function() {
    $(".deleteLink").click(function () {
        var $a = $(this);
        $.confirm('确认删除？', function () {
            $.post($a.attr('href'), {name: $a.data('key')}, function (msg) {
                if (msg.state) {
                    $.alert(msg.info, function () {
                        $a.parents('tr').remove();
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