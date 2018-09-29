<?php common\components\View::regCss('login.css') ?>

<?php $form = self::beginForm(['showLabel' => false]) ?>
    <div class="container bg">
        <div class="row">
            <div class="col-xs-12 text-center">
                <div class="tx_img">
                    <img src="" width="546" height="364"> </div>
            </div>
        </div>
        <div class="row">
            <div class="login_input">
                <div class="type_box login_type_ls mg_b">
                    <div class="type_name"><img src="/images/phone.png" width="18" height="18"></div>
                    <div class="input_box">
                        <?= $form->field($model, 'mobile')->textInput(['placeholder' => '手机号'])  ?>
                    </div>
                </div>
            </div>
            <div class="login_input">
                <div class="type_box login_type_ls mg_b">
                    <div class="type_name"><img src="/images/psd_one.png" width="18" height="18"></div>
                    <div class="input_box">
                        <?= $form->field($model, 'password')->passwordInput(['placeholder' => '请输入六位密码'])  ?>
                    </div>
                </div>
            </div>
            <div class="login_input">
                <div class="type_box login_type_ls mg_b">
                    <div class="type_name"><img src="/images/psd.png" width="18" height="18"></div>
                    <div class="input_box">
                        <?= $form->field($model, 'cfmPassword')->passwordInput(['placeholder' => '确认密码']) ?>
                    </div>
                </div>
            </div>
            <div class="login_input">
                <div class="type_box login_type_ls mg_b">
                    <div class="type_name"><img src="/images/yz.png" width="18" height="18"></div>
                    <div class="input_box"><a class="pull-right" id="verifyCodeBtn" data-action="<?= url('site/verifyCode') ?>" data-mobile="#user-mobile" data-captcha="#user-captcha">发送验证码</a>
                        <input type="text" id="user-verifycode" style="width:70%;" class="form-control" name="User[verifyCode]" placeholder="验证码">
                    </div>
                </div>
            </div>
        </div>
        <div class="reg_btn">
            <a href="<?= url('site/login') ?>">返回登录</a>
            <button id="submitBtn">完成注册</button>
        </div>
    </div>

<!-- 遮罩层开始 -->
<div class="transmask">
    <div class="infotips">你的信息已提交,正在审核<br/>请耐心等待审核</div>
</div>

<!-- 遮罩层结束 -->

<?php self::endForm() ?>

<script>
$(function () {
    $("#submitBtn").click(function () {
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
        var mobile = $('#user-mobile').val();
        var url = $(this).data('action');
        if (mobile.length != 11) {
            $.alert('您输入的不是一个手机号！');
            return false;
        }
        $.post(url, {mobile: mobile}, function(msg) {
                $.alert(msg.info);
        }, 'json');
    });
});
</script>