<?php $this->regCss('yanzheng.css') ?>

<div class="forget-box">
    <?php $form = self::beginForm(['showLabel' => false]) ?>
    <div class="title">初次使用，请设置商品密码</div>
    <div class="content-wrap">
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => '请设置6~12位密码', 'class' => 'textvalue'])  ?>
        <?= $form->field($model, 'cfmPassword')->passwordInput(['placeholder' => '确认密码', 'class' => 'textvalue regPassword']) ?>
        <p id="errorMsg"></p>
        <a class="btn-sure disabled" id="submitBtn">确定</a>
    </div>
    <?php self::endForm() ?>
</div>

<script>
$(function () {
    var $inputs = $('.regPassword');
    $inputs.keyup(function() {
        if ($inputs.val().length > 5) {
            $('#submitBtn').removeClass('disabled');
        } else {
            $('#submitBtn').addClass('disabled');
        }
    });

    //提交
    $("#submitBtn").click(function () {
        if ($(this).hasClass('disabled')) {
            return false;
        }
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    $.alert(msg.info);
                } else {
                    window.location.href = msg.info;
                }
            }
        }));
        return false;
    });
});
</script>       