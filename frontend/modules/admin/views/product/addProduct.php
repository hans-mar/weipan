<?php $form = self::beginForm() ?>
<?= $model->title('特殊产品') ?>
<?= $form->field($model, 'table_name')->textInput(['placeholder' => '产品的全拼音']) ?>
<?= $form->field($model, 'name') ?>
<?= $form->field($model, 'fee')->textInput(['placeholder' => '手续费金额'])?>
<?= $form->field($model, 'unit')->textInput(['placeholder' => '产品点位波动单位'])?>
<?= $form->field($model, 'unit_price')->textInput(['placeholder' => '一波动单位的人民币金额'])?>
<?= $form->field($model, 'maxrise')->textInput(['placeholder' => '最大止盈'])?>
<?= $form->field($model, 'maxlost')->textInput(['placeholder' => '最大止损'])?>
<?= $form->field($model, 'unit_price_na')->textInput(['placeholder' => '一波动单位的原币金额'])?>
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