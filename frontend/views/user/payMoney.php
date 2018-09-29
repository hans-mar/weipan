<?php $this->regCss('geren.css') ?>
<style>
body {
    font: 14px/1.5 arial,tahoma,sans-serif,"\5B8B\4F53";
}
</style>
<div class="personal">
    <header class="apage-header">
        <div class="content">
            <h3>我的账户-充值</h3>
            <!-- <div class="left">
                <a href="javascript:window.history.back()" class="iconfont icon-xiangzuojiantou"></a>
            </div> -->
        </div>
    </header>
    <section class="page-main main-withdrawal" style="background:#fff">
        <div class="content">
            <form id="login-form" action="" method="post">          
            <ul class="mod-list">
                <li>
                    <label>
                        <span>订单详情：账户充值</span>
                    </label>
                </li>
                <li>
                    <label>
                        <span>订单金额：￥<em style="color:#f00;font-size:50px">5000</em>元</span>
                    </label>
                </li>
            </ul>
            </form>
            <div style="background-color: #ccc; height:40px;padding-left: 10px; line-height: 40px; width: 100%; ">选择的支付方式</div>
            <div style="margin: 10px 0;padding-left: 10px; width: 100%"><img src="/images/WePayLogo.png" style="width: 110px;/* height:70px; */"></div>
            <div style="margin: 35px auto; width: 210px">
                <button style="margin:auto 0px;width:210px; height:40px; border-radius: 5px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" id="payBtn" onclick="callpay()">立即支付</button>
            </div>
        </div>
    </section>

</div>

<script type="text/javascript">
    //调用微信JS api 支付
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?= $jsApiParameters; ?>,
            function(res){
               // alert(res.err_code+res.err_desc+res.err_msg); 
               if (res.err_msg == "get_brand_wcpay_request:ok"){  
                   window.location.href="<?= url(['user/index', 'trade_no' => $userCharge->trade_no]) ?>";  
               } else {
                   window.location.href="<?= url('user/recharge') ?>";  
               }
            }
        );
    }

    function callpay()
    {
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        }else{
            jsApiCall();
        }
    }
</script>

