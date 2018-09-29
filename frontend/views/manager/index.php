<?php $this->regCss('manager.css') ?>

<div id="main">
    <div class="index-box">
        <div class="info clearfix">
            <div class="wxImg">
                <img src="<?= u()->face ?>">
            </div>
            <p class="realName nameAndTel"><?= $extend->realname ?></p>
            <p class="tel nameAndTel"><?= $extend->mobile ?></p>
            <a href="<?= url(['manager/income']) ?>" style="display: block;">
            	<p class="myIncome nameAndTel">
	            	<span class="myIncome">我的收入</span>
	            </p>
	            <p class="nameAndTel myIncome-num-wrap boxflex">
		            <span class="myIncome-num box_flex_1"><?= $extend->rebate_account ?></span>
		            <i class="earrow earrow-right"></i>
	            </p>
            </a>
            
        </div>
        <div class="meun-box menuBox">
            <div class="menu-wrap">
        		<a href="<?= url(['manager/customer']) ?>" style="display: block;">
	                <div class="menu-item customer boxflex" data-index="0">
	                    <i class="icon icon-customer"></i>
	                    <span class="menu-itemTitle">直属客户</span>
	                    <span class="box_flex_1 menu-itemNum"><?= $userNum ?>人</span>
	                    <i class="earrow earrow-right"></i>
	                </div>
        		</a>
        		<a href="<?= url(['manager/cover']) ?>" style="display: block;">
	                <div class="menu-item coverings boxflex" data-index="2">
	                    <i class="icon icon-coverings"></i>
	                    <span class="menu-itemTitle menu-cover">客户平仓</span>
	                    <span class="box_flex_1 menu-itemNum"><?= $orderNum ?>笔</span>
	                    <i class="earrow earrow-right"></i>
	                </div>
        		</a>
        		<a href="<?= url(['manager/card']) ?>" style="display: block;">
	                <div class="menu-item boxflex" data-index="3">
	                    <i class="icon icon-mycard"></i>
	                    <span class="menu-itemTitle menu-id">我的名片</span>
	                    <span class="box_flex_1 menu-itemNum"></span>
	                    <i class="icon icon-ewm"></i>
	                    <i class="earrow earrow-right"></i>
	                </div>
        		</a>
            </div>
        </div>
    </div>
</div>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    //屏蔽所有右上角功能
    function onBridgeReady(){
        WeixinJSBridge.call('hideOptionMenu');
    }

    if (typeof WeixinJSBridge == "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
            document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
        }
    } else {
        onBridgeReady();
    }

</script>