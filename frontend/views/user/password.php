<?php common\components\View::regCss('yanzheng.css') ?>

<div class="forget-box">
    <div class="title">修改商品密码</div>
    <div class="content-wrap">
    <?php $form = self::beginForm(['showLabel' => false]) ?>
        <?= $form->field($model, 'oldPassword')->passwordInput(['placeholder' => '请输入原密码', 'class' => 'textvalue'])?>
        <?= $form->field($model, 'newPassword')->passwordInput(['placeholder' => '请输入6-18位字母或数字', 'class' => 'textvalue']) ?>
        <?= $form->field($model, 'cfmPassword')->passwordInput(['placeholder' => '请再次输入密码', 'class' => 'textvalue']) ?>
        <a class="btn-sure" id="submitBtn">确定</a>
    <?php self::endForm() ?>
    </div>
</div>

<script>
$(function () {
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    $.alert(msg.info);
                } else {
                    $.alert(msg.info);
                    window.location.href = '<?= url(['user/index']) ?>'
                }
            }
        }));
        return false;
    });
});
</script>