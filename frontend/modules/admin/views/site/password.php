<?php $form = self::beginForm(['id' => 'passwordForm', 'action' => self::createUrl(['password'])]) ?>
<?= $form->field($model, 'oldPassword')->passwordInput() ?>
<?= $form->field($model, 'newPassword')->passwordInput() ?>
<?= $form->field($model, 'cfmPassword')->passwordInput() ?>
<?= $form->submit('确认修改', ['id' => 'passwordBtn']) ?>
<?php self::endForm() ?>

<script>
$(function () {
    $("#passwordBtn").click(function () {
        $("#passwordForm").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '修改成功', function () {
                        window.location.reload();
                    });
                } else {
                    $.alert(msg.info);
                }
            }
        }));
        return false;
    });
});
</script>