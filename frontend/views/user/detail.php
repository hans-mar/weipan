<?php common\components\View::regCss('login.css') ?>
<?php common\components\View::regCss('iconfont/iconfont.css') ?>
<?php use frontend\models\Order;?>

<style type="text/css">body{background: #191919;font-size: 1.6rem;}</style>
<div class="container">
    <div class="liq_box">
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p>
                        <font class="liq_font">订单号：</font>BX000<?= $order->id ?></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">建仓时间：</font>
                        <span><?= $order->created_at ?></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">建仓金额:</font>
                        <span id="make"><?= $order->deposit ?></span>￥ </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="liq_box_hx text-left">
                    <div class="col-xs-6 liq_border">
                        <font class="liq_font">入仓价:</font>
                        <span><?= $order->price ?></span>
                    </div>
                    <div class="col-xs-6">
                        <font class="liq_font">平仓价格：</font>
                        <span><?= $order->sell_price ?></span>
                    </div>
                </div>
                <div class="liq_box_hx text-left">
                    <div class="col-xs-6 liq_border liq_borders">
                        <font class="liq_font pull-left">手续费(每手)：</font>
                        <span class="styled-select">
                    <?= $order->fee / $order->hand ?>￥
                  </span>
                    </div>
                    <div class="col-xs-6  liq_borders">
                        <font class="liq_font">方向：</font>
                        <?php $style='color:green';$string='跌↓';if ($order->rise_fall == Order::RISE) { $style = 'color:red';$string='涨↑';} ?>
                        <span class="styled-select"><span style="<?= $style ?>"><?= $string ?></span> </span>
                    </div>
                </div>
                <div class="liq_box_hx text-left">
                    <div class="col-xs-6 liq_border liq_borders">
                        <font class="liq_font pull-left">止盈：</font>
                        <span class="styled-select">
                    <?= $order->stop_profit_point ?>%      
                  </span>
                    </div>
                    <div class="col-xs-6  liq_borders">
                        <font class="liq_font">止损：</font>
                        <span class="styled-select">              
                  <?= $order->stop_loss_point ?>%    
                  </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">手续费:</font><?= $order->fee ?>￥
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">盈亏金额:</font>
                        <?php $style='color:green';if ($order->profit > 0) { $style = 'color:red';} ?>
                        <span style="<?= $style ?>"><?= $order->profit ?>￥</span> </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        本单盈余:
                        <font class="liq_font" id="endcash" style="<?= $style ?>"><?= $order->sell_deposit ?>￥</font>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>