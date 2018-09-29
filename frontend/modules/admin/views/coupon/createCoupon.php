<?php $form = self::beginForm() ?>
<?= $model->title('体验券') ?>
<?= $form->field($model, 'coupon_type')->dropDownList() ?>
<?= $form->field($model, 'amount')->textInput(['placeholder' => '单位（元）']) ?>
<?= $form->field($model, 'valid_day')->textInput(['placeholder' => '单位（天）']) ?>
<?= $form->submit() ?>
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