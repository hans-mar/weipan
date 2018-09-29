<div style="margin-top: 10px;">
    <div style="margin: 10px 0;">缓存个数：<?= count($cache) ?></div>
    <input type="button" value="清除缓存" id="clearCache">
</div>
<script>
$(function () {
    $("#clearCache").click(function () {
        $.post('', {}, function (msg) {
            $.alert(msg.info, function () {
                location.reload();
            });
        }, 'json');
    });
});
</script>