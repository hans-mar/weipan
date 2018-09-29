<?php use common\helpers\Html; ?>
<?php common\assets\HighStockAsset::register($this) ?>
<?php $this->regJs('candle') ?>
<?php $this->regCss('jiaoyi') ?>
<?php $this->regCss('trade') ?>
<?php $this->regCss('geren') ?>
<?php $this->regCss('common.css') ?>
 <style type="text/css">
 body{
    width:100vw;
    overflow-x:hidden;
 }
 div{
    overflow-x: hidden;
 }
  @keyframes sponde{
    0%{width:6px;height:6px;opacity:1;}
    100%{width:30px;height:30px;opacity:0;}
  }
  @-webkit-keyframes sponde{
    0%{width:6px;height:6px;opacity:1;}
    100%{width:30px;height:30px;opacity:0;}
  }
  @-moz-keyframes sponde{
    0%{width:6px;height:6px;opacity:1;}
    100%{width:30px;height:30px;opacity:0;}
  }
  @-ms-keyframes sponde{
    0%{width:6px;height:6px;opacity:1;}
    100%{width:30px;height:30px;opacity:0;}
  }
  @-o-keyframes sponde{
    0%{width:6px;height:6px;opacity:1;}
    100%{width:30px;height:30px;opacity:0;}
  }
  .aniContainer{
    position:absolute;
    width:50px;
    height:50px;
    display:flex;
    display:-webkit-box;/* android 2.1-3.0, ios 3.2-4.3 */
    display:-webkit-flex;/* Chrome 21+ */
    align-items:center;
    justify-content:center;
    -webkit-box-pack: center;/* android 2.1-3.0, ios 3.2-4.3 */
    -webkit-box-align: center;/* android 2.1-3.0, ios 3.2-4.3 */
    /*bottom: 94px;*/
    right: -14px;
    /*visibility:hidden;*/
    transition: all 0.4s;
    -webkit-transition: all 0.4s;
    /*opacity:0;*/
  }
  .aniContainer .core{
    position:absolute;
    width:3px;
    height:3px;
    border-radius:50%;
    background:#7CB5EC;
    top:24px;
    left:24px;
  }
  .aniContainer .aniBorder{
      border-radius:50%;
     /* border:6px solid #7CB5EC;*/
     background:#7CB5EC;
      -webkit-animation:sponde 1s infinite;
     -moz-animation:sponde 1s infinite;
     -ms-animation:sponde 1s infinite;
     -o-animation:sponde 1s infinite;
     animation:sponde 1s infinite;
  }
  .my-btn-group{
    position: absolute;
    right: 10px;
    top: 50px; 
    z-index: 10000;
    visibility: hidden;
  }
  .my-btn-group button{
    width: 30px;
    height: 20px;
    border-radius: 4px;
    color: #fff;
    cursor: pointer;
  }
  .myButtom ul{
    margin-top:5px;
  }
  #shclColor{
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: 100000;
    background:#fff url(../images/t01254dd604a647794b.gif) no-repeat center center;
  }
</style>
    <div>
        <div class="transaction indexContent">
            <div class="tra_ad" style="display: none;">
                <a href=""><img src=""></a>
            </div>
            <!--账户资产-->
            <div class="boxflex assets-wrap">
                <div class="userinfo-wrap">
                    <a href="<?= url(['user/index']) ?>"><img src="<?= u()->face ?>"></a>
                    <p style="font-size:12px;">个人中心</p>
                </div>
                <div class="cash-asset box_flex_1">
                    <div class="asset">
                        资产<span id="userProfit" style="color:#eb7d12;"><?= u()->account - u()->blocked_account ?></span>元
                    </div>
                    <div style="margin-top:3px;" class="btn-withdraw-wrap">
                        <div class="recharge overallPsd" data-url="<?= url(['user/recharge', 'user_id' => u()->id]) ?>"><span>充值</span></div>
                        <!-- <div class="recharge overallPsd" data-url="<?= url(['site/tip']) ?>"><span>充值</span></div> -->
                        <div class="withdraw overallPsd" data-url="<?= url(['user/withDraw']) ?>"><span>提现</span></div>
                   
                    </div>
                </div>
                <!--账户资产-金币-->
                <div class="coin-asset orderCountDown" <?php if ($time >= 15) {$style = 'hidden';$time=15;$disClass='';} else {$style='visible';$disClass='disabled';} ?> style="visibility: <?= $style ?>;">
                    <label>下单倒计时</label>
                    <div>
                        <i class="icon-clock"></i>
                        <span class="redsymbol countDown">00:<?= $time?></span>
                    </div>
                </div>
            </div>
            <!-- 商品列表 -->
            <div class="goodslist-wrap">
                <ul class="boxflex selectProcut">
                    <?php foreach ($productArr as $key => $value): ?>
                    <li class="box_flex_1 <?php if ($key == $product->id){ echo 'active';} ?>" data-pid="<?= $key ?>" data-name="<?= $value['table_name'] ?>" data-close="<?= $value['close'] ?>" style="width: 33%; text-align: left">
                    <?php $class='down';if ($value['price'] > $value['close']){ $class = 'up';} $price=$value['price']; if($value['source'] == 1 && (date('w') == 0 || date('w') == 6)){$price = '休市';}?>
                        <div class="gooddetail"><?= $value['name'] ?><br><span class="price price-<?= $class ?>"><span><?= $price ?></span>
                        <?php if ($price != '休市'): ?>
                            <i class="arrow arrow-<?= $class ?>"></i>
                        <?php endif ?></span></div>
                    </li>
                    <?php endforeach ?>
                </ul>
            </div>
            <!-- 商品详情以及走势图K线 -->
            <div class="goodinfo-wrap">
                <div class="goodinfo boxflex">
                    <span class="key">昨收:</span>
                    <span class="value closeprice"><?= floatval($newData['close']) ?></span>
                    <span class="box_flex_1"></span>
                    <span class="key">今开:</span>
                    <span class="value openprice"><?= floatval($newData['open']) ?></span>
                    <span class="box_flex_1"></span>
                    <span class="key">最高:</span>
                    <span class="value maxprice"><?= floatval($newData['high']) ?></span>
                    <span class="box_flex_1"></span>
                    <span class="key">最低:</span>
                    <span class="value minprice"><?= floatval($newData['low']) ?></span>
                </div>
                <div style="position:relative">
                    <div id="shclColor"></div>
                    <p class="my-btn-group">
                        <button id="minus-btn">-</button>
                        <button id="back-btn">back</button>
                        <button id="plus-btn">+</button>
                    </p>
                    <div id="areaContainer" style=" min-width: 310px; width:100%;margin-top:-40px"></div>
                    <div id="kContainer" style=" min-width: 310px; width:100%; display: none;margin-top:-40px"></div>
                    <div class="aniContainer">
                       <span class="core"></span>
                       <span class="aniBorder"></span>
                    </div>
                </div> 

                <div class="graph-kind">
                    <ul id="feature-tab" class="boxflex" style="width: 100%; transition-timing-function: cubic-bezier(0.1, 0.57, 0.1, 1); transition-duration: 500ms; transform: translate(0px, 0px) translateZ(0px);">
                        <li class="box_flex_1 active"><a data-value="" data-unit="-1">今日走势</a></li>
                        <!-- <li class="box_flex_1"><a data-value="1" data-unit="0">1M</a></li> -->
                        <!-- <li class="box_flex_1"><a data-value="2" data-unit="1">5M</a></li> -->
                        <li class="box_flex_1"><a data-value="5" data-unit="2">15分钟k线</a></li>
                        <li class="box_flex_1"><a data-value="6" data-unit="3">30分钟k线</a></li>
                        <li class="box_flex_1"><a data-value="3" data-unit="4">60分钟k线</a></li>
                        <!-- <li class="box_flex_1"><a data-value="10" data-unit="5">日K线</a></li> -->
                    </ul>
                </div>
            </div>

            <div class="deal-btn-wrap <?= $disClass ?>">
                <div class="table">
                    <div class="table-cell btnrise-wrap buyProduct" data-type="1">
                        <!-- <label><span><img src="/images/icon-up.png" alt="" style="margin-top: -8px;"></span></label> -->
                        <label><span>买入</span></label>
                    </div>
                    <div class="table-cell btndown-wrap buyProduct" data-type="2">
                        <!-- <label><span><img src="/images/icon-down.png" alt="" style="margin-top: -8px;"></span></label> -->
                        <label><span>卖出</span></label>
                    </div>
                </div>
                <p><?= config('web_trade_time', '商品时间：周一~周五早上6:00~凌晨4:00 周末休市') ?></p>
            </div>
           <!--  <div class="holdlist-wrap"><ul></ul></div> -->
        </div>
        <div class="footer_cont table">
            <i class="table-cell arrow arrow-tip"></i>
            <span class="table-cell">
                <marquee direction="left" scrollamount="3" class="active"><?= config('web_scroll', '利用大数据行情，抓住大波动收益机会！&nbsp;&nbsp;&nbsp;&nbsp;温馨提示：投资有风险，入市需谨慎！&nbsp;&nbsp;&nbsp;&nbsp;提现时间为每周一至周五上午9：30-10：30    下午：2：30-5：30') ?></marquee>

            </span>
        </div>
        <div class="myContent">

        </div>
        <div class="myButtom">
            <div class="holdlist-wrap">
                <ul>
                <?= $this->render('_orderList', compact('orders')) ?>
                </ul>
            </div>
        </div>
    </div>
<input type="hidden" id="productId" value="<?= $product->id ?>">
<div id="jsonData" style="display: none;"><?= $json ?></div>
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

$(function() {
    setTimeout(function(){
        $("#shclColor").css("display","none");
    },500);
    //倒计时
    var wait = <?= $time ?>;
    function time(obj) {
        if (wait == -1) {
            $('.orderCountDown').css('visibility', 'hidden');
            $('.deal-btn-wrap').removeClass('disabled');
            wait = 15;
            return false;
        }
        if (wait < 10) {
            obj.html('00:0' + wait);
        } else {
            obj.html('00:' + wait);
        }
        wait--;
        setTimeout(function() {
            time(obj);
        },
        1000);
    }
    //订单是否在倒计时之内
    <?php if ($time < 15): ?>
        setTimeout(function() {
            time($('.countDown'));
        },
        1000);
    <?php endif ?>

    $('.buys a').click(function() {
        $(this).addClass("selects").siblings().removeClass("selects");
    })
    //三个产品的切换
    $('.selectProcut>li').click(function() {
        $('.selectProcut>li').removeClass("active");
        $(this).addClass("active");
        window.location.href = "/site/index?pid="+$(this).data('pid');
    })

    //买涨买跌
    $('.buyProduct').click(function() {
        if ($('.deal-btn-wrap').hasClass('disabled')) {
            return $.alert('下单后15秒之内不能再次下单！');
        }
        var data = {};
        data.pid = <?= $product->id ?>;
        data.type = $(this).data('type');
        $.post("<?= url('site/ajaxBuyState')?>", {data: data}, function(msg) {
            if (msg.state) {
                if (msg.data == -1) {
                    window.location.href = msg.info;
                } else {
                    $('.myContent').append(msg.info);
                    $('body').find('.footer_cont').remove();    
                }
            } else {
                $.alert(msg.info);
            }
        }, 'json');
    })

    //下单
    $(".myContent").on("click", '.payOrder', function() {
        var data = {};
        data.hand = parseInt($('.myContent').find('.hand').val());
        data.deposit = $('.myContent .deposit').find('.active').html();
        //止盈止损点数
        // data.stop_profit_point = $('.myContent .stop_profit_point').val();
        // data.stop_loss_point = $('.myContent .stop_loss_point').val();
        data.point = $('.myContent .point').find('.active').html();
        
        //产品id
        data.product_id = <?= $product->id ?>;
        data.rise_fall = $(this).data('type');
        $.post("<?= url('order/ajaxSaveOrder')?>", {data: data}, function(msg) {
            if (msg.state) {
                // window.location.href = '<?= url(['order/position']) ?>';
                var money = parseFloat($('#userProfit').html());
                $('#userProfit').html(parseFloat(money - msg.data));
                $('.orderCountDown').css('visibility', 'visible');
                $('.deal-btn-wrap').addClass('disabled');
                $.alert('购买成功！');
                $('.myContent').html('');
                $('.myButtom .holdlist-wrap>ul').html(msg.info);
                time($('.countDown'));
                $('.myContent').before('<div class="footer_cont table"><i class="table-cell arrow arrow-tip"></i><span class="table-cell"><marquee direction="left" scrollamount="3" class="active">利用大数据行情，抓住大波动收益机会！&nbsp;&nbsp;&nbsp;&nbsp;温馨提示：投资有风险，入市需谨慎！&nbsp;&nbsp;&nbsp;&nbsp;提现时间为每周一至周五上午9：30-10：30    下午：2：30-5：30</marquee> </span></div><div>');
            } else {
                $('.right .deposit_price').attr('price', data.price_rate * data.hand);
                $.alert(msg.info);
                // if (msg.info == '您的余额已不够支付，请充值！') {
                //    window.location.href = '<?= HX_PAY_DOMAIN . url(['user/pay']) ?>'; 
                // }
            }
        }, 'json');
    });
});
</script>
