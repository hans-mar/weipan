<?php use common\helpers\Html;
      use frontend\models\Product; ?>
<?php $this->regCss('iconfont/iconfont.css') ?>
<?php $this->regCss('list.css') ?>
<?php $this->regJs('order.js') ?>

    <!--中间内容-->
    <div class="container">
        <div class="row">
            <ul class="nav nav-tabs buy-tab payType" id="tabs">
                <li class="active tab1"  data-type="1">
                    <a href="javascript:void(0);">
                        <img src="/images/Buyup_reflect.png" width="40" height="40" class="img1">
                        <img src="/images/Buy_2.png" width="40" height="40" style="display: none;" class="img2">
                        <span>使用现金</span>
                    </a>
                </li>
                <li class="tab2" data-type="2">
                    <a href="javascript:void(0);">
                        <img src="/images/Buyup_card.png" width="40" height="40" class="img2">
                        <img src="/images/Buy_1.png" width="40" height="40" style="display: none;" class="img1">
                        <span>使用体验劵</span>
                    </a>
                </li>
            </ul>
            <div class="tab-con">
                <!--使用现金-->
                <div class="tab-page" style="display:block">
                    <div class="feature pad_t20 mar_0">
                        <div class="cash-box pad_b20">
                            <div class="cash-box-width">
                                <div class="col-xs-6 pad_t10">
                                    <p class="mar">现价</p>
                                    <p><span class="co_r curPrice"><?= $product->dataAll->price ?></span></p>
                                </div>
                                <div class="col-xs-6 pad_t10">
                                    <p class="mar">可用</p>
                                    <p><span class="co_r"><?= u()->account - u()->blocked_account ?></span>元</p>
                                </div>
                            </div>
                            <div class="cash-box-width  text-left">
                                <p class="pad_t10">花费（手/元）</p>
                                <div class="col-xs-12 mar">
                                    <p class="price-btn">
                                    <?php $i = 1;$class='';foreach ($productPrice as $array) : ?>
                                        <a href="javascript:void(0);" class="btn-cash <?php if ($i==1) {echo 'price-bg';} elseif($i==3) {echo 'pull-right text-center mar_l5';} elseif($i==4) {echo 'pull-right text-center';} ?>" data-fee="<?= $array['fee'] ?>" data-profit="<?= $array['one_profit'] ?>" data-id="<?= $array['id'] ?>" data-max="<?= $array['max_hand'] ?>"><?= floatval($array['deposit']) ?></a>
                                    <?php $i++;endforeach; ?>
                                    </p>
                                </div>
                                <div class="col-xs-6 mar">
                                    <p class="pad_t10 font_10 mar_b0">资金放大约<span class="co_r">80</span>倍</p>
                                    <p class="pad_t10 font_10 co_r">●波动一个点即盈利<span class="productProfit"><?= $productPrice[0]['one_profit'] ?></span>元</p>
                                </div>
                                <div class="col-xs-6 mar text-center">
                                    <p class="pad_t10 font_10 mar_b0">资金放大约<span class="co_r">40</span>倍</p>
                                </div>
                            </div>
                            <div class="cash-box-width bor-none">
                                <p class="text-left pad_t10">购买<span class="pad_l20"><?= $product->name ?>,当前价格<em class="co_r curPrice"><?= $product->dataAll->price ?></em></span></p>
                                <!-- <p class="text-left pad_t10">购买<span class="pad_l20">白银40.0千克,市场价<em class="co_r">632838</em>元</span></p> -->
                                <p class="text-left">手续费<span class="pad_l20 productFee"><?= $productPrice[0]['fee'] ?></span>手/元</p>

                                <div class="col-xs-2 pad_l0 pad_t8 text-left">
                                    <span>手数</span>
                                </div>
                                <div class="col-xs-6 text-left mar box">
                                    <div class="items" style="border:1px solid #0FFFF4;">
                                        <a href="javascript:void(0);" class="pull-left minus" style="border-right:1px solid #0FFFF4;"><i class="iconfont text-jc font_12" style="color:#0FFFF4;">&#xe760;</i></a>
                                        <input type="text" name="hand" value="1" class="text-center hand" data-max="<?= $productPrice[0]['max_hand'] ?>" readonly="readonly">
                                        <a href="javascript:void(0);" class="pull-right plus" style="border-left:1px solid #0FFFF4;"><i class="iconfont text-jc font_12"  style="color:#0FFFF4;">&#xe610;</i></a>
                                        <span class="num_tip max_hand" data-max="<?= $productPrice[0]['max_hand'] ?>">1</span>
                                    </div>
                                </div>
                                <div class="col-xs-4 pad_l0 pad_t10 pad_r0 text-right">
                                    <span class="font_10 co_e max_hand">(范围：1-<?= $productPrice[0]['max_hand'] ?>)</span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-xs-2 pad_l0 pad_t8 text-left">
                                    <span>止盈</span>
                                </div>
                                <div class="col-xs-6 text-left mar box">
                                    <div class="items">
                                        <a href="javascript:void(0);" class="pull-left minus"><i class="iconfont text-jc font_12">&#xe760;</i></a>
                                        <input type="text" id="profit" value="0" class="text-center" readonly="readonly">
                                        <a href="javascript:void(0);" class="pull-right plus"><i class="iconfont text-jc font_12">&#xe610;</i></a>
                                        <span class="num_tip stopProfit">0</span>
                                    </div>
                                </div>
                                <div class="col-xs-4 pad_l0 pad_t10 pad_r0 text-right">
                                    <span class="font_10 co_e">(范围：0-50%)</span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-xs-2 pad_l0 pad_t8 text-left">
                                    <span>止损</span>
                                </div>
                                <div class="col-xs-6 text-left mar box">
                                    <div class="items items-bor">
                                        <a href="javascript:void(0);" class="pull-left minus minus-bor"><i class="iconfont text-jc co_g font_14">&#xe760;</i></a>
                                        <input type="text" id="loss" value="0" class="text-center" readonly="readonly">
                                        <a href="javascript:void(0);" class="pull-right plus push-bor"><i class="iconfont text-jc co_g font_14">&#xe610;</i></a>
                                        <span class="num_tip stopLoss">0</span>
                                    </div>
                                </div>
                                <div class="col-xs-4 pad_l0 pad_t10 pad_r0 text-right">
                                    <span class="font_10 co_e">(范围：0-50%)</span>
                                </div>
                                <div class="clearfix"></div>
                                <div classs="col-xs-12">
                                    <p class="font_10 co_e">投资有风险，商品需谨慎，做好止损，降低风险</p>
                                </div>
                                <div class="col-xs-12">
                                    <div class="buy-btn-box">
                                        <a href="javascript:void(0);" class="buy-btn-sure co_f payOrder">确定</a>
                                        <a href="<?= url('site/index') ?>" class="buy-btn-sure bg_f">取消</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cash-box mar_t10 mar_b30">
                            <div class="col-xs-2">
                                <p class="font_20 pad_t20">注：</p>
                            </div>
                            <div class="col-xs-10 pad_r0 pad_t10 po_re">
                                <ul class="font_12 text-left">
                                    <li>1.暂时不支持持仓过夜,收盘后将自动平仓</li>
                                    <li>2.只能持仓一笔,需平仓后才可建新仓</li>
                                    <li>3.粤贵银商品更灵活,无以上限制</li>
                                </ul>
                                <img src="/images/Buyup_exit.png" width="20" height="20" class="cash-img">
                            </div>
                        </div>
                    </div>
                </div>
                <!--使用银元卷-->
                <div class="tab-page" style="display:none;">
                    <div class="feature pad_t20 mar_0">
                        <div class="cash-box pad_b20">
                            <div class="cash-box-width bor-none text-left">
                                <div class="col-xs-12 pad_t10 pad_l0">
                                    <p class="mar">现价<span class="pad_l10 curPrice"><?= $product->dataAll->price ?></span></p>
                                </div>
                                <div class="col-xs-12 pad_t10 pad_l0 numberType">
                                    <p class="mar">卷种
                                        <?php foreach ($couponType as $key => $value) : ?>
                                        <span class="pad_l10 po_re display_in">
                                            <img src="/images/Buy_3.png" width="55" height="30"  class="img3" style="display:none;">
                                            <img src="/images/Buy_4.png" width="55"  class="img4" height="30" >
                                            <font class="volume numberVolume" style="left:45%;" data-number="<?= $value ?>"><?= $key ?></font>
                                        </span>
                                        <?php endforeach ?>
                                    </p>
                                </div>
                                <div class="col-xs-12 pad_t10 pad_l0">
                                    <p class="mar">可用
                                        <span class="pad_l10"><em class="numberCoupon">0</em>张
                                            <em class="co_e font_10">(每笔建仓最多使用10张)</em>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-xs-12 pad_t10 pad_l0">
                                    <p class="mar">花费(张)
                                        <span class="styled-select">                  
                                            <select name="tnumber" id="tnumber">
                                                <option value="1">1张</option>
                                                <option value="2">2张</option>
                                                <option value="3">3张</option>
                                                <option value="4">4张</option>
                                                <option value="5">5张</option>
                                                <option value="6">6张</option>
                                                <option value="7">7张</option>
                                                <option value="8">8张</option>
                                                <option value="9">9张</option>
                                                <option value="10">10张</option>
                                            </select>
                                            <i class="iconfont icon-triangle">&#xe66d;</i>                    
                                        </span>
                                    </p>
                                </div>
                                <div class="col-xs-12 pad_t10 pad_l0">
                                    <p class="mar">提示
                                        <span class="pad_l10">
                                            体验券对应现金购买时的波动
                                        </span>
                                    </p>
                                </div>
                                <div class="col-xs-12 pad_t10 pad_l0">
                                    <p class="mar">手续费
                                        <span class="pad_l10">
                                            无
                                        </span>
                                    </p>
                                </div>
                                <div class="col-xs-2 pad_l0 pad_t8 text-left">
                                    <span>止盈</span>
                                </div>
                                <div class="col-xs-6 text-left mar box">
                                    <div class="items">
                                        <a href="javascript:void(0);" class="pull-left minus"><i class="iconfont text-jc font_12">&#xe760;</i></a>
                                        <input type="text" id="tprofit" value="0" class="text-center" readonly="readonly">
                                        <a href="javascript:void(0);" class="pull-right plus"><i class="iconfont text-jc font_12">&#xe610;</i></a>
                                        <span class="num_tip tstopProfit">0</span>
                                    </div>
                                </div>
                                <div class="col-xs-4 pad_l0 pad_t10 pad_r0 text-right">
                                    <span class="font_10 co_e">(范围：0-50%)</span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-xs-2 pad_l0 pad_t8 text-left">
                                    <span>止损</span>
                                </div>
                                <div class="col-xs-6 text-left mar box">
                                    <div class="items items-bor">
                                        <a href="javascript:void(0);" class="pull-left minus minus-bor"><i class="iconfont text-jc co_g font_14">&#xe760;</i></a>
                                        <input type="text" id="tloss" value="0" class="text-center" readonly="readonly">
                                        <a href="javascript:void(0);" class="pull-right plus push-bor"><i class="iconfont text-jc co_g font_14">&#xe610;</i></a>
                                        <span class="num_tip tstopLoss">0</span>
                                    </div>
                                </div>
                                <div class="col-xs-4 pad_l0 pad_t10 pad_r0 text-right">
                                    <span class="font_10 co_e">(范围：0-50%)</span>
                                </div>
                                <div class="clearfix"></div>
                                <div classs="col-xs-12">
                                    <p class="font_10 co_e">投资有风险，商品需谨慎，做好止损，降低风险</p>
                                </div>
                                <div class="col-xs-12">
                                    <div class="buy-btn-box">
                                        <a href="javascript:void(0);" id="tickt" class="buy-btn-sure co_f payOrder">确定</a>
                                        <a href="<?= url('site/index') ?>" class="buy-btn-sure bg_f">取消</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cash-box mar_t10 mar_b30">
                            <div class="col-xs-2 text-left">
                                <p class="font_12 pad_t20">注：</p>
                            </div>
                            <div class="col-xs-10 text-left mar">
                                <p class="font_12 pad_t10">体验卷仅为一次性本金使用，盈利后您只能获得其中利益，而亏损您无需承担。体验券可通过分享好友加入获得。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--底部导航 begin-->

    <script type="text/javascript">
    $(function() {
        //保证金种类
        var productPrice = jQuery.parseJSON('<?= json_encode($productPrice) ?>');
        //下单
        $('.payOrder').click(function() {
            var data = {};
            data.type = $('.payType .active').data('type');
            //下单类型1是现金下单
            if (data.type == 1) {
                data.priceId = $('.price-btn').find('.price-bg').data('id');
                //手数
                data.hand = parseInt($('.hand').val());
                //触发止盈止损点数
                data.stop_profit_point = parseInt($('.stopProfit').html());
                data.stop_loss_point = parseInt($('.stopLoss').html());
            } else {
                //体验金额
                data.deposit = $('.numberType').find('.co_on').html();
                var allNumber = $('.numberCoupon').html();
                //购买数量
                data.hand = $('#tnumber').val();
                if (data.hand > allNumber || data.hand < 1) {
                    return $.alert('体验卷的数量非法！');
                }
                if (data.hand > 10) {
                    return $.alert('体验卷的数量不能超过10个！');
                }
                var bool = false;
                for(var i in productPrice) {
                    if (parseInt(productPrice[i].deposit) == data.deposit) {
                        bool = true;
                    }
                }
                if (!bool) {
                    return $.alert('体验卷种不合适！');
                }
                //触发止盈止损点数
                data.stop_profit_point = parseInt($('.tstopProfit').html());
                data.stop_loss_point = parseInt($('.tstopLoss').html());
            }
            // return tes(data);
            //产品id
            data.product_id = <?= $product->id ?>;
            //买涨
            data.rise_fall = <?= get('type') ?>;
            $.post("<?= url('order/ajaxSafeOrder')?>", {data: data}, function(msg) {
                if (msg.state) {
                    $.alert(msg.info);
                    window.location.href = '<?= url('order/position') ?>';
                } else {
                    $('.right .deposit_price').attr('price', data.price_rate * data.hand);
                    $.alert(msg.info);
                    if (msg.info == '您的余额已不够支付，请充值！') {
                       window.location.href = '<?= url('user/recharge') ?>'; 
                    }
                }
            }, 'json');
        });
        //期货数据跳动
        function futuresPrice(){
            //是否属于期货
            var futures = '<?= $product->table_name ?>';
            $.post("<?= url('site/ajaxNewProductPrice')?>", {data: futures}, function(msg) {
                if (msg.state) {
                    $('.curPrice').html(msg.info.price);
                } else {
                    tes(msg.info);
                }
            }, 'json');
        }
        setInterval(futuresPrice, 1000);
    });
    </script>




