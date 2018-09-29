<?php use common\helpers\Hui; ?>
<?php admin\assets\LoginAsset::register($this) ?>

<div class="header"></div>
<div class="login-wraper">
    <div id="loginform" class="login-box">
    <?php $form = self::beginForm(['class' => ['form', 'form-horizontal']]) ?>
        <?= $form->field($model, 'username')->textInput(['placeholder' => $model->label('username'), 'class' => ['input-text', 'size-L']])->label('<i class="Hui-iconfont">&#xe60d;</i>', ['class' => ['form-label', 'col-xs-3']]) ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->label('password'), 'class' => ['input-text', 'size-L']])->label('<i class="Hui-iconfont">&#xe60e;</i>', ['class' => ['form-label', 'col-xs-3']]) ?>
        <?= $form->field($model, 'captcha', ['template' => "<div class='formControls col-xs-8 col-xs-offset-3'>{input}</div>\n{hint}\n{error}"])->captcha(['options' => ['placeholder' => '验证码', 'style' => ['width' => '150px'], 'class' => ['input-text', 'size-L']]])->label(false) ?>
        <?= $form->field($model, 'rememberMe', ['template' => "<div class='formControls col-xs-8 col-xs-offset-3'>{input}</div>\n{hint}\n{error}"])->checkbox(['label' => '记住我']) ?>
        <?= $form->submit('登 录', ['style' => ['width' => '100px'], 'class' => ['size-L', 'mt-20', 'mb-20']]) ?>
    <?php self::endForm() ?>
    </div>
</div>
<div class="footer"><?= config('web_copyright') ?></div>

<script>
$(function () {
    // 首次登陆隐藏验证码，登陆失败后才出现
    ;!function () {
        if ('<?= session('requireCaptcha') ?>') {
            $("#loginform-captcha").parents('.row').show();
        } else {
            $("#loginform-captcha").parents('.row').hide();
        }
    }();
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    $.alert(msg.info, function () {
                        $("#loginform-captcha").parents('.row').show();
                    });
                }
            }
        }));
        return false;
    });
});
</script>
