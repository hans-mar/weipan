<?php $this->regCss('geren.css') ?>
<div class="personal">
    <p class="charge-header"> <a href="javascript:window.history.back()" style="float: left;"><img src="/images/arrow-left.png" style="width:40px;"></a><span>充值</span></p>
    <div class="boxflex boxflex1">
        <div class="img-wrap"><img class="userimage" src="/test/4.jpg"></div>
        <div class="box_flex_1">
            <div class="p_zichan"><?= u()->nickname ?></div>
        </div>
    </div>
    <?php $form = self::beginForm(['showLabel' => false, 'action' => url('user/pay/'), 'id' => 'payform']) ?>
    <div class="boxflex1 mt10">
        <div class="moneyhead">充值金额</div>
        <!-- <div class="group_btn clearfloat">
            <div class="btn_re">
                <a class="btn_money active">50000</a>
            </div>
            <div class="btn_re btn_center">
                <a class="btn_money">30000</a>
            </div>
            <div class="btn_re btn_center">
                <a class="btn_money">20000</a>
            </div>
            <div class="btn_re">
                <a class="btn_money">10000</a>
            </div>
            <div class="btn_re">
                <a class="btn_money">5000</a>
            </div>
        </div> -->
        <div class="group_btn group clearfloat">
            <div class="btn_re">
                <a class="btn_money active">4980</a>
            </div>
            <div class="btn_re btn_center">
                <a class="btn_money">3505</a>
            </div>
            <div class="btn_re btn_center">
                <a class="btn_money">1997</a>
            </div>
            <div class="btn_re btn_center">
                <a class="btn_money">501</a>
            </div>
            <!-- <div class="btn_re btn_center">
                <a class="btn_money">0.01</a>
            </div> -->
           
            <input type="hidden" id="amount" name="amount" value="4980">
            <!-- <input type="hidden" id="type" name="type" value="3"> -->
        </div>
    </div>
<!--    <div class="boxflex1 mt10">-->
<!--        <div class="moneyhead">充值金额</div>-->
<!--    </div>-->
    <!-- <div class="boxflex1" style="border-top:none">
        <img src="/images/icon-chat.png" />
        <span>微信支付</span>
        <input type="radio" name="type" value="2" style="float:right;padding: 5px 0;"/>
    </div> -->
    <div class="boxflex1" style="border-top:none">
        <img src="/images/zfb.png" />
        <span>支付宝支付</span>
        <input type="radio" name="type" value="1" style="float:right;padding: 5px 0;" checked/>
    </div>
    <!-- <div class="recharge-btn mt10 payMoney">立即充值</div> -->

<!--    <img src="/images/icon-chat.png" />微信  <-->
<!--    <br />-->
<!--    <img src="/images/icon-chat.png" />支付宝  <input type="radio" name="pay" value="z" />-->
<!--    < class="recharge-btn mt10" id="payBtn" name="wx" value="1">微信充值</>-->
    <div class="recharge-btn mt10" id="payBtn" name="wx" value="1">立即充值</div>
<!--    <div class="recharge-btn mt10" id="payBtn" name="zfb" value="2">支付宝充值</div>-->
    <?php self::endForm() ?>
</div>

<form style='display:none;' id='formpay' name='formpay' method='post' action='https://pay.paysapi.com'>
    <input name='goodsname' id='goodsname' type='text' value='' />
    <input name='istype' id='istype' type='text' value='' />
    <input name='key' id='key' type='text' value='' />
    <input name='notify_url' id='notify_url' type='text' value='' />
    <input name='orderid' id='orderid' type='text' value='' />
    <input name='orderuid' id='orderuid' type='text' value='' />
    <input name='price' id='price' type='text' value='' />
    <input name='return_url' id='return_url' type='text' value='' />
    <input name='uid' id='uid' type='text' value='' />
    <input type='submit' id='submitdemo1'>
</form>

<script type="text/javascript">
    $(".btn_money").click(function(){
        $('.clearfloat .btn_money').removeClass("active");
        $('#amount').val($(this).html());
        $(this).addClass("active");
    });

    $('#payBtn').on('click', function(){
        var amount = $('#amount').val();
        var type = $('input[name="type"]').val();
        console.log(amount);
        console.log(type);

        $.ajax({
                url: "<?= url('user/pay')?>",
                data: {
                    amount: amount,
                    type: type,
                },
                type: 'post',
                dataType: 'json',
                success: function (res) {
                    console.log(res);
                    if (res.code > 0) {
                        $("#goodsname").val(res.data.goodsname);
                        $("#istype").val(res.data.istype);
                        $('#key').val(res.data.key);
                        $('#notify_url').val(res.data.notify_url);
                        $('#orderid').val(res.data.orderid);
                        $('#orderuid').val(res.data.orderuid);
                        $('#price').val(res.data.price);
                        $('#return_url').val(res.data.return_url);
                        $('#uid').val(res.data.uid);
                        $('#submitdemo1').click();

                    } else {
                        alert(res.msg);
                    }

                }
            });
        // $("#payform").submit();
    });


</script>
