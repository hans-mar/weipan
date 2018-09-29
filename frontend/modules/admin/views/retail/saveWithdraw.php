<?php $form = self::beginForm() ?>
<?= $model->title('代理商申请出金') ?>
<?= $form->field($retail, 'account')->textInput(['disabled' => 'disabled'])->label('代理商账号') ?>
<?= $form->field($retail, 'total_fee')->textInput(['disabled' => 'disabled'])->label('可提现额度') ?>
<?= $form->field($adminAccount, 'bank_name') ?>
<?= $form->field($adminAccount, 'bank_card') ?>
<?= $form->field($adminAccount, 'bank_user') ?>
<?= $form->field($adminAccount, 'bank_mobile') ?>
<?= $form->field($adminAccount, 'bank_address') ?>
<?= $form->field($model, 'amount') ?>
<?= $form->submit($model) ?>
<?php self::endForm() ?>

<script>
$(function () {
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '操作成功', function () {
                        parent.location.reload();
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