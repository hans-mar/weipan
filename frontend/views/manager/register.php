<?php use frontend\models\User; ?>
<?php $this->regCss('manager.css') ?>

<div id="main">
    <?php $form = self::beginForm(['showLabel' => false]) ?>
    <div class="register-box">
        <div class="input-wrap boxflex">
            <i class="icon icon-tel"></i>
            <?= $form->field($userExtend, 'mobile')->textInput(['placeholder' => '请输入您的手机号码', 'class' => 'box_flex_1 register-tel regTel'])  ?>
        </div>
        <div class="input-wrap boxflex">
            <i class="icon icon-name"></i>
            <?= $form->field($userExtend, 'realname')->textInput(['placeholder' => '请输入您的真实姓名', 'class' => 'box_flex_1 register-name'])  ?>
        </div>
        <div class="input-wrap boxflex">
            <i class="icon icon-pwd"></i>
            <?= $form->field($userExtend, 'coding')->textInput(['placeholder' => '请输入邀请码', 'class' => 'box_flex_1 orgcode'])  ?>
        </div>
        <div class="input-wrap boxflex">
            <i class="icon icon-code"></i>
            <?= $form->field($userExtend, 'verifyCode')->textInput(['placeholder' => '请输入手机验证码', 'class' => 'box_flex_1 register-code regCode'])  ?>
            <div class="btn-sendcode" id="verifyCodeBtn" data-action="<?= url(['site/verifyCode']) ?>">获取验证码
            </div>
        </div><p id="errorMsg"></p>
        <div class="btn-regsubmit disabled" id="submitBtn">提交</div>
    </div>
    <?php self::endForm() ?>
</div>

<!-- 遮罩层开始 -->
<?php if (u()->apply_state == User::APPLY_STATE_WAIT): ?>
<div class="transmask">
    <div class="infotips">你的信息已提交,正在审核<br/>请耐心等待审核</div>
</div>
<?php endif ?>
<!-- 遮罩层结束 -->
<script>
$(function () {
    var $inputs = $('.regCode');
    $inputs.keyup(function() {
        if ($inputs.val().length > 3) {
            $('#submitBtn').removeClass('disabled');
        } else {
            $('#submitBtn').addClass('disabled');
        }
    });
    //倒计时
    var wait = 60;
    function time(obj) {
        if (wait == 0) {
            obj.removeClass('disabled');           
            obj.html('重新获取验证码');
            wait = 60;
        } else {
            obj.addClass('disabled');
            obj.html('重新发送(' + wait + ')');
            wait--;
            setTimeout(function() {
                time(obj);
            },
            1000)
        }
    }
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
    // 验证码
    $("#verifyCodeBtn").click(function () {
        if ($(this).hasClass('disabled')) {
            return false;
        }
        var mobile = $('.regTel').val();
        var url = $(this).data('action');
        if (mobile.length != 11) {
            $.alert('您输入的不是一个手机号！');
            return false;
        }
        $.post(url, {mobile: mobile}, function(msg) {
                if (msg.state) {
                    time($('#verifyCodeBtn'));
                } else {
                    $.alert(msg.info);
                }
        }, 'json');
    });
});
</script>