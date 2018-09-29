<?php use common\helpers\Hui; ?>
<?php $form = self::beginForm() ?>
<?= Hui::H2('待创建列表', ['class' => 'text-c', 'style' => 'padding: 0 0 20px;']) ?>
<?= $html ?>
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