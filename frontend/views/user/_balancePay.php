        <?php foreach ($data as $userCharge) :?>
        <div class="row">
            <div class="detail_main">
                <div class="detail_box_hx">
                    <div class=" left_img">
                        <img src="/images/money.png">
                    </div>
                    <div class="right_text">
                        <div class="detail_text">
                            <div class="tot yellow">
                                <font class="pull-left">充值订单号：<?= $userCharge->trade_no ?></font>
                                <font class="pull-right"><?= $userCharge->amount ?>￥</font>
                            </div>
                            <br>
                            <div class="tot">
                                <font class="pull-left">来源:<?= $userCharge->chargeTypeValue ?></font>
                                <font class="pull-right"><?= $userCharge->created_at ?></font>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach ?>