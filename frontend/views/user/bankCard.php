<?php $this->regCss('geren.css') ?>
<?php $this->regCss('main.css') ?>
<style type="text/css">
    html{
        height: 100vh;
        background: #F4F4F4;
    }
    body{
       background:  #F4F4F4;
    }
    .form-group{
        position: relative;
        height: 50px;
        line-height: 50px;
        background: #fff;
        margin-bottom: 0;
    }
    .form-group+.form-group{
        margin-top: 12px;
    }
    .form-group:before{
        content:"开户银行";
        font-size:14px;
        color: #828284;
        display: inline-block;
        width: 90px;
        margin-left: 10px;
        margin-right: 10px;
        text-align: right;
    }
    .form-group:nth-child(3):before{
        content:"身份证号";
    }
    .form-group:nth-child(4):before{
        content:"银行卡号";
    }
    .form-group:nth-child(5):before{
        content:"持卡人姓名";
    }
    .form-group:nth-child(6):before{
        content:"预留手机号";
    }
    .form-group:nth-child(7):before{
        content:"短信验证码";
    }
    .form-group input{
        font-size: 14px;
        color: #474747;
        padding-left: 10px;


        display: inline-block;
        width: 54%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: transparent;
        background-image: none;
        border:0 ;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0);
        box-shadow: inset 0 1px 1px rgba(0,0,0,0);
        -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    }
    .code.fr{
        position: absolute;
        height: 30px;
        line-height: 30px;
        right: 12px;
        top: 321px;
        color: #828284;
        background: transparent;
    }
    #submitBtn{
        color: #fff;
        font-size: 15px;
        background: #00E5B8;
        width: 90%;
        height: 44px;
        line-height: 44px;
        border-radius:6px;
        margin-left: 5%;
        margin-top: 60px;
        bottom:200px;
    }
</style>
<?php $form = self::beginForm(['showLabel' => false]) ?>
    <?= $form->field($bankCard, 'bank_name')->textInput(['placeholder' => '请输入银行名称']) ?>
    <?= $form->field($bankCard, 'id_card')->textInput(['placeholder' => '请输入身份证号']) ?>
    <?= $form->field($bankCard, 'bank_card')->textInput(['placeholder' => '请输入银行卡号']) ?>
    <?= $form->field($bankCard, 'bank_user')->textInput(['placeholder' => '请输入持卡人姓名']) ?>
    <?= $form->field($bankCard, 'bank_mobile')->textInput(['placeholder' => '请输入银行卡预留手机号']) ?>
    <!-- <?= $form->field($bankCard, 'verifyCode')->textInput(['placeholder' => '请输入短信验证码']) ?> -->
    <button type="submit" id="submitBtn" class=" col-xs-12 navbar-fixed-bottom text-center footer_bg font_16">提交</button>
    <!-- <input type="button" class="code fr" value="获取手机验证码"  id="verifyCodeBtn" data-action="<?= url(['site/verifyCode']) ?>"> -->
<?php self::endForm() ?>

<script>
$(function () {
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    $.alert(msg.info);
                } else {
                    $.alert(msg.info, function(){
                    window.location.href = '<?= url('user/index') ?>'
                        
                    });
                }
            }
        }));
        return false;
    });
    // 验证码
    // $("#verifyCodeBtn").click(function () {
    //     var mobile = $('.bank_mobile').val();
    //     var url = $(this).data('action');
    //     if (mobile.length != 11) {
    //         $.alert('您输入的不是一个手机号！');
    //         return false;
    //     }
    //     $.post(url, {mobile: mobile}, function(msg) {
    //           $.alert(msg.info);
    //     }, 'json');
    // })
});
</script>