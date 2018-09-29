<?php common\components\View::regCss('login.css') ?>
    <div class="container">
        <?php $form = self::beginForm(['showLabel' => false]) ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="logo_img">
                        <img src="<?= config('web_logo') ?>" alt="<?= config('web_name') ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="login_input">
                    <div class="type_box login_type_ls mg_b">
                        <div class="type_name"><img src="/images/phone.png" width="18" height="18"></div>
                        <div class="input_box">
                            <?= $form->field($model, 'username')->textInput(['placeholder' => '手机号']) ?>
                        </div>
                    </div>
                </div>
                <div class="login_input">
                    <div class="type_box login_type_ls mg_b">
                        <div class="type_name"><img src="/images/psd_one.png" width="18" height="18"></div>
                        <div class="input_box">
                            <?= $form->field($model, 'password')->passwordInput(['placeholder' => '密码']) ?>
                        </div>
                    </div>
                </div>
<!--                 <div class="login_input">
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                </div> -->
                <div class="login_input mr_t">
                    <!-- <span class="pull-right"><a href="<?= url('site/register') ?>">注册新用户</a></span> -->
                    <span class="pull-right"><a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=<?= WX_APPID ?>&redirect_uri=http%3a%2f%2f<?= $_SERVER['HTTP_HOST'] ?>/site/register&response_type=code&scope=snsapi_userinfo&state=index#wechat_redirect">注册新用户</a></span>
                    <br/><span class="pull-right"><a href="<?= url('site/forget') ?>">忘记密码</a></span>
                </div>
            </div>
<!--             <div class="login_btn">
                <button id="submitBtn">登录</button>
            </div> -->
            <div class="submitBtn">
                登录
            </div>
        <?php self::endForm() ?>
    </div>
<script>
$(function () {
    $(".submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    return $.alert(msg.info);
                } else {
                    window.location.href = msg.info;
                }
            }
        }));
        return false;
    });
});
</script>