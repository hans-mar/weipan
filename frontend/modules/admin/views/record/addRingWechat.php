<?php $form = self::beginForm() ?>
<?= $ringWechat->title('微会员公众号信息') ?>
<?= $form->field($ringWechat, 'admin_id')->textInput(['placeholder' => '请输入微会员的账号'])->label('归属微会员') ?>
<?= $form->field($ringWechat, 'ring_name')->label('微会员名称') ?>
<?= $form->field($ringWechat, 'url') ?>
<?= $form->field($ringWechat, 'appid') ?>
<?= $form->field($ringWechat, 'appsecret') ?>
<?= $form->field($ringWechat, 'mchid') ?>
<?= $form->field($ringWechat, 'mchkey') ?>
<?= $form->field($ringWechat, 'token') ?>
<?= $form->field($ringWechat, 'sign_name') ?>
<?= $form->submit($ringWechat) ?>
<?php self::endForm() ?>

<script>
$(function () {
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (msg.state) {
                    $.alert('操作成功', function () {
                        parent.location.reload();
                    });
                } else {
                    $.alert(msg.info);
                }
            }
        }));
        return false;
    });
    //用户类型的切换
    $('#adminuser-power').on('change', function() {
        var power = $(this).val();
        $.post("<?= url(['admin/ajaxSubUser']) ?>", {power: power}, function(msg) {
            if (msg.state) {
                $('.myPid').html(msg.info);
            } else {
                $.alert(msg.info);
            }
        }, 'json');
    });
});
</script>