<!-- <link rel="stylesheet" type="text/css" href="/css/login.css" /> -->
<?php common\components\View::regCss('login.css') ?>
<?php common\components\View::regCss('bootstrap/css/bootstrap.min.css') ?>

<div class="container">
    <div class="liq_box">
        <div class="row">
            <div class="liq_main">s
                <div class="col-xs-12 liq_box_hx">
                    <p>
                        <font class="liq_font">订单号&nbsp;&nbsp;:</font>
                        JY1000<?= $order->id ?></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">建仓时间&nbsp;&nbsp;:</font>
                        <span><?= $order->created_at ?></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">建仓金额&nbsp;&nbsp;:</font>
                        <span id="make"><?= $order->deposit ?></span>￥ </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="liq_box_hx text-left">
                    <div class="col-xs-6 liq_border">
                        <font class="liq_font">入仓价&nbsp;&nbsp;:</font>
                        <span id="buy"><?= $order->price ?></span>
                    </div>
                    <div class="col-xs-6">
                        <font class="liq_font" id="pcode" value="<?= $order->one_profit ?>"><?= $order->product->name ?>现价&nbsp;&nbsp;:</font>
                        <span class="liq_red price" value="">计算中</span>
                    </div>
                </div>
                <div class="liq_box_hx text-left">
                    <div class="col-xs-6 liq_border liq_borders">
                        <font class="liq_font pull-left">波动&nbsp;&nbsp;:</font>
                        <span class="styled-select">
                            &nbsp;&nbsp;<?= $order->one_profit ?>元/点
                        </span>
                    </div>
                    <div class="col-xs-6  liq_borders">
                        <font class="liq_font">方向&nbsp;&nbsp;:</font>
                        <span class="styled-select">
                            <?php if ($order->rise_fall == 1) : ?>              
                            <span id="ostyle" value="0" style="color:red">涨↑</span> 
                            <?php else: ?>
                            <span id="ostyle" value="1" style="color:green">跌↓</span> 
                            <?php endif ?>
                        </span>
                    </div>
                </div>
                <div class="liq_box_hx text-left">
                    <div class="col-xs-6 liq_border liq_borders">
                        <font class="liq_font pull-left" id="endwin">止盈&nbsp;&nbsp;:</font>
                        <span class="styled-select">&nbsp;&nbsp;
                            <?= $order->stop_profit_price / $order->deposit * 100 ?>%   
                               
                        </span>
                    </div>
                    <div class="col-xs-6  liq_borders">
                        <font class="liq_font">止损&nbsp;&nbsp;:</font>
                        <span class="styled-select" id="endloss"> &nbsp;&nbsp;             
                          <?= $order->stop_loss_price / $order->deposit * 100 ?>%    
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">手续费&nbsp;&nbsp;:</font>
                        ￥<?= $order->fee ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">盈亏金额:</font>
                        <span class="profit" style="color:green">计算中</span>
                        <span class="profitRate" style="color:green">计算中</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font ">本单盈余:</font>
                        <span class="deposit" style="color:green">计算中</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row liq_tx">
        <div class="col-xs-1 pad_r0">
            <img src="/images/warning.png" width="16" height="16">
        </div>
        <div class="col-xs-11">
            <p>如该订单在结算时间（凌晨04：00)前未平仓，将会被强行平仓。</p>
        </div>
    </div>
    <div class="row">
        <div class="liq_btn">
            <button class="sellOrder">确认平仓</button>
        </div>
    </div>
</div>
    <script>
    $(function() {
        //确认平仓
        $('.sellOrder').click(function() {
            $.post("<?= self::createUrl(['order/ajaxSellOrder'])?>", {id: '<?= $order->id ?>'}, function(msg) {
                if (msg.state == 1) {
                    $.alert(msg.info);
                    window.location.href = '<?= url(['order/position']) ?>';
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        });

        //持仓数据跳动
        function updateOrder(){
            $.post("<?= url('order/ajaxUpdateOrderOne')?>", {id: '<?= $order->id ?>'}, function(msg) {
                if (msg.state) { 
                    var obj = msg.info;
                    $('.price').html(obj['price']);
                    if (obj['profit'] >= 0) {
                       $('.profit').css('color', 'red');
                       $('.profitRate').css('color', 'red');
                       $('.deposit').css('color', 'red');
                    } else {
                       $('.profit').css('color', 'green');
                       $('.profitRate').css('color', 'green');
                       $('.deposit').css('color', 'green');
                    }
                    $('.profit').html('￥' + obj['profit']);
                    $('.profitRate').html(obj['profitRate'] + '%');
                    $('.deposit').html('￥' + obj['deposit']);
                }
            }, 'json');
        }
        setInterval(updateOrder, 1000);
    })
    </script>