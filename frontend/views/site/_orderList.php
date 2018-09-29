<?php foreach ($orders as $order): ?>
        <li class="hold-item table" data-id="<?= $order->id ?>">
            <div class="table-cell">
                <div class="value"><?php $class='f_die';$str='空';if($order->rise_fall == 1) {$class='f_zhang';$str='多';}?><span class="direct <?= $class ?>"><?= $str ?></span></div>
                <div class="key"><?= $order->product->name ?></div>
            </div>
            <div class="table-cell">
                <div class="value"><?= $order->hand ?></div>
                <div class="key">数量</div>
            </div>
            <div class="table-cell">
                <div class="value"><?= floatval($order->price) ?></div>
                <div class="key">建仓价</div>
            </div>
            <div class="table-cell">
                <div class="value"><span class="profit"><?= floatval($order->deposit) ?></span></div>
                <div class="key">定金</div>
            </div>
            <div class="table-cell">
                <div class="value"><span class="profit"><?= floatval($order->stop_profit_price) ?></span></div>
                <div class="key">止盈止损</div>
            </div>
        </li>
<?php endforeach ?>
