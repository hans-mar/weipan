<?php use frontend\models\Order; ?>
<?php common\components\View::regCss('iconfont/iconfont.css') ?>
<?php common\components\View::regCss('list.css') ?>
    <!--头部导航-->
    <div class="container bg">
        <div class="row mar font_14">
            <div class="col-xs-6 co_f pad_l0 pad_t5 pad_b5">
                <!--  <p class="pad_t10">总盈亏:(元)<span class="co_r pad_l5 " id="total">0</span></p>         -->
            </div>
            <div class="col-xs-6 pad_r0  pad_t5 pad_b5">
                <!-- <p class="text-right pad_t10">交易记录<i class="iconfont pad_l5">&#xe64d;</i></p> -->
            </div>
        </div>
    </div>
    <div class="container bg_h">
        <div class="row">
            <div class="col-xs-12 mar">
                <ul class="list-group-box over_hidd">
                    <li>盈亏</li>
                    <li>建仓价</li>
                    <li>产品</li>
                    <li>操作</li>
                </ul>
            </div>
        </div>
    </div>
        <?php foreach ($orders as $order) : ?>
        <div class="row order">
            <div class="col-xs-12 mar">
                <ul class="list-item over_hidd" data-id="<?= $order->id ?>">
                    <li>
                        <p class="co_g pad_t5 profit" wavepoint="" value="1">计算中</p>
                    </li>
                    <li class="pad_t5"><span id="cash0" value="<?= $order->price ?>"><?= $order->price ?></span>
                        <span class="<?php if ($order->rise_fall == Order::RISE) {echo 'co_r';$str = '多';} else {echo 'co_g';$str = '空';} ?> ">(<?= $str ?>)</span>
                    </li>
                    <li class="pcode pad_t5" value="<?= $order->product->table_name ?>"><?= $order->product->name ?></li>
                    <li>
                        <p class="pc_list">
                            <a href="<?= url(['order/sellPosition', 'id' => $order->id]) ?>">
                                <img src="/images/cqd_2.png" width="32" height="32" alt="平仓">
                            </a>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
        <?php endforeach ?>
    <script>
    $(function() {
        $(".modal-dialog-list li p").click(function() {
            $(".modal-dialog-list li p").removeClass("dialog-list-bg");
            $(this).addClass("dialog-list-bg")

        });

        $(".modal-dialog-list2 li p").click(function() {
            $(".modal-dialog-list2 li p").removeClass("dialog-list-bg");
            $(this).addClass("dialog-list-bg")

        });

        //提交止盈止损参数
        // $('.saveOrderPoint').click(function() {
        //     var data = {};
        //     data.profit = $('.win .dialog-list-bg').data('value');
        //     data.loss = $('.los .dialog-list-bg').data('value');
        //     data.id = $(this).data('id');
        //     $.post("<?= self::createUrl('order/ajaxSaveOrderPoint')?>", {data: data}, function(msg) {
        //         if (msg.state == 1) {
        //             alert(msg.info);
        //             $('#myModal').hide();
        //         } else {
        //             alert(msg.info);
        //         }
        //     }, 'json');
        // });

        //持仓数据跳动
        function updateOrder(){
            $.post("<?= url('order/ajaxUpdateOrder')?>", function(msg) {
                if (msg.state) { 
                    var obj = msg.info;
                    //对页面所有数据进行修改
                    $('.order .mar>ul').each(function(){
                        //被系统平仓的订单消失
                        var order_id = Number($(this).data('id'));
                        // tes(order_id,idArr,$.inArray(order_id, idArr));return;
                        //判断此持仓id是否被系统平仓
                        if (obj[order_id] == undefined) {
                            $(this).remove();
                        }
                        var $this = $(this).find('.profit');
                        $this.html(obj[order_id]);
                        if (obj[order_id] > 0) {
                           $this.addClass('co_r'); 
                           $this.removeClass('co_g'); 
                        } else {
                           $this.removeClass('co_r'); 
                           $this.addClass('co_g'); 
                        }
                    });
                }
            }, 'json');
        }
        setInterval(updateOrder, 1000);
    })
    </script>
    <style type="text/css">
    .modal-dialog-list2 li {
        float: left;
        width: 16%;
    }
    .dialog-list2 {
        text-align: center;
        line-height: 40px;
        border: 1px solid #999;
        color: #fff;
        border-radius: 50%;
        width: 40px;
        height: 40px;
    }
    </style>
<!--     <script type="text/javascript">
    var nowoid = 0;
    $(".setBtn").click(function() {
        nowoid = $(this).data('id');
        var profit = $("#endprofit" + nowoid).val();
        var loss = $("#endloss" + nowoid).val();
        if (profit > 0) {
            $(".dialog-list.dialog-list-bg").removeClass('dialog-list-bg');
        }
        if (loss > 0) {
            $(".dialog-list2.dialog-list-bg").removeClass('dialog-list-bg');
        }
        $('.saveOrderPoint').data('id', $(this).data('id'));
        $(".dialog-list[data-value='" + profit + "']").addClass('dialog-list-bg');
        $(".dialog-list2[data-value='" + loss + "']").addClass('dialog-list-bg');
    })
    </script> -->