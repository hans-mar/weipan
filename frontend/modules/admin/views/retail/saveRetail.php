<?php $form = self::beginForm() ?>
<?= $model->title('会员单位') ?>
<?= u()->power < 9999 ?'':$form->field($adminUser, 'pid')->dropDownList()->label('选择综合会员') ?>
<?= $form->field($model, 'account') ?>
<?= $form->field($model, 'pass') ?>
<?= $form->field($model, 'company_name') ?>
<?= $form->field($model, 'realname') ?>
<?= $form->field($model, 'tel') ?>
<?php //$form->field($model, 'qq') ?>
<?php //$form->field($model, 'file1')->upload() ?>
<?php //$form->field($model, 'file2')->upload() ?>
<?php //$form->field($model, 'file3')->upload() ?>
<?php //$form->field($model, 'file4')->upload() ?>
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