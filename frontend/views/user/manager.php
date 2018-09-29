<?php common\components\View::regCss('iconfont2/iconfont.css') ?>
<?php common\components\View::regCss('iconfont/iconfont.css') ?>
<?php common\components\View::regCss('uplode.css') ?>
<?php common\components\View::regCss('bootstrap.min.css') ?>
<style type="text/css">body{background:#fff;}</style>
    <?php $form = self::beginForm(['showLabel' => false]) ?>
        <div class="container">
            <div class="row pad_10 list-bom2 fix-head">
                <div class="col-xs-3">
                    <a href="<?= url('user/index') ?>" class="back-icon"><i class="iconfont">&#xe64e;</i></a>
                </div>
                <div class="col-xs-6 back-head">申请材料</div>
                <div class="col-xs-3"></div>
            </div>
            <div class="pay_box mar_t">
                <div class="row title_bg">
                    <div class="text-center pay_title">提交身份证信息</div>
                    <div class="col-xs-12 pay_text">
                        <p>申请经纪人资格需填写材料，详细规则请点击阅读:<a href="<?= url('user/managerRule') ?>"><span style="color:red">《经纪人规则》
                    </span></a></p>
                    </div>
                </div>
                <div class="row inputs_box">
                    <div class="type_box login_type_ls bor">
                        <div class="input_box"><span>提交身份证信息</span></div>
                        <div class="type_name"><i class="iconfont">&#xe67f;</i></div>
                    </div>
                    <div id="person" style="margin-left:20px;"></div>
                    <div class="col-xs-12 name_input">
                        <div class="input_del">
                            <?= $form->field($userAccount, 'realname')->textInput(['placeholder' => '输入真实姓名']) ?>
                        </div>
                        <div class="input_del">
                            <?= $form->field($userAccount, 'id_card')->textInput(['placeholder' => '输入身份证号']) ?>
                        </div>
                    </div>
                </div>
                <div class="row space">
                    身份证仅用于申请备案，不对外公开
                </div>
                <div class="row inputs_box">
                    <div class="type_box login_type_ls bor">
                        <div class="type_name"><i class="iconfont">&#xe636;</i></div>
                        <div class="input_box"><span>提交银行卡信息</span></div>
                        <div id="patient" style="font-size:15px;"></div>
                    </div>
                    <div id="bank" style="margin-left:20px"></div>
                    <div class="col-xs-12 name_input">
                        <div>
                            <?= $form->field($userAccount, 'bank_name')->dropDownList() ?>
                        </div>
                        <div class="input_del">
                            <?= $form->field($userAccount, 'bank_address')->textInput(['placeholder' => '请填写收款银行，需完整填写省、市、支行名称']) ?>
                        </div>
                        <div class="input_del">
                            <?= $form->field($userAccount, 'bank_card')->textInput(['placeholder' => '输入银行卡号']) ?>
                        </div>
                    </div>
                </div>
                <div class="row space">
                    银行卡用于提成发放，请使用借记卡
                </div>
                <div class="row inputs_box">
                    <div class="row space">
                        <!--    仅需提供公司盖章那页面（最后一页） -->
                        <p style="font-size:6px; color:red;padding: 0 10px;">小提示:请务必填写真实有效信息，否则审核将不予以通过，对于填写虚假信息情节严重着将予以封号和相应的法律法规处理</p>
                    </div>
                </div>
                <div>
                    <?= $form->field($userAccount, 'bank_mobile')->textInput(['placeholder' => '输入银行卡号', 'type' => 'hidden', 'value' => u()->id]) ?>
                    <button type="submit" id="submitBtn" class=" col-xs-12 navbar-fixed-bottom text-center footer_bg font_16">我已经填写完毕提交审核</button>
                </div>
            </div>
        </div>
    <?php self::endForm() ?>

<script>
$(function () {
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    $.alert(msg.info);
                } else {
                    $.alert(msg.info);
                    window.location.href = '<?= url('user/index') ?>'
                }
            }
        }));
        return false;
    });
});
</script>