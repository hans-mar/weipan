    <div class="transaction transaction1 orderContent">
        <div class="removeClass">
        </div>
        <div id="createorderbox">
            <div class="createorder-content">
                <div class="createchoose-wrap" <?= $class ?>>建仓看<?= $string ?></div>
                <div class="key-value boxflex">
                    <div id="definecashnum" class="box_flex_1"></div>
                </div>
                <div class="key-value boxflex">
                    <label class="key">合约定金:</label>
                    <div class="box_flex_1" id="setting-point">
                        <ul class="table deposit">
                        <?php $i=1;foreach ($productPrice as $product): ?>
                            <li <?php if ($i==1): ?>class="active"<?php endif ?> data-id="<?= $product['id']?>"><?= floatval($product['deposit']) ?></li>
                        <?php $i++;endforeach ?>
                        </ul>
                    </div>
                </div>
                <div class="key-value boxflex">
                    <label class="key">数量:</label>
                    <div class="box_flex_1 num-wrap">
                        <span class="btn-coin btn-minute unable-click" data-value="-1">-</span>
                        <input type="tel" value="1" onpaste="return false" data-max="<?= $product['max_hand'] ?>" oncontextmenu="return false" oncopy="return false" oncut="return false" class="hand" readOnly="true" >
                        <span class="btn-coin btn-add" data-value="1">+</span>
                    </div>
                </div>
                <div class="key-value boxflex">
                        <label class="key">止盈/止损点:</label>
                        <div class="box_flex_1" id="setting-point">
                            <ul class="table point">
                            <?php $i=1;foreach ($productPrice as $product) {$productProfit[$product['id']] = floatval($product['one_profit']);} 
                            $productProfit = array_unique($productProfit); ?>
                            <?php foreach ($productProfit as $k => $v): ?>
                                <li <?php if ($i==1): ?>class="active"<?php endif ?> data-id="<?= $k ?>"><?= $v ?></li>
                            <?php $i++;endforeach ?>
                            </ul>
                        </div>
                </div>
<!--                 <div class="key-value boxflex">
                    <label class="key">止盈:</label>
                    <div class="box_flex_1" id="setting-point">
                        <div class="box_flex_1 num-wrap">
                        <span class="btn-coined btn-minuteed" data-value="-10" >-</span>
                        <input type="tel" value="0" onpaste="return false" data-max="50" oncontextmenu="return false" oncopy="return false" oncut="return false" class="stop_profit_point handed" readOnly="true" >
                        <span class="btn-coined btn-added" data-value="10" >+</span>
                        <span class="scope">（范围：0~50%）</span>
                    </div>
                    </div>
                </div>
                <div class="key-value boxflex">
                    <label class="key">止亏:</label>
                    <div class="box_flex_1" id="setting-point">
                        <div class="box_flex_1 num-wrap">
                        <span class="btn-coineded btn-minuteeded" data-value="-10">-</span>
                        <input type="tel" value="0" onpaste="return false" data-max="50" oncontextmenu="return false" oncopy="return false" oncut="return false" class="stop_loss_point handeded" readOnly="true" >
                        <span class="btn-coineded btn-addeded" data-value="10">+</span>
                        <span class="scope">（范围：0~50%）</span>
                    </div>
                    </div>
                </div> -->
                <div class="sure-btn-wrap">
                    <div class="table">
                        <div class="table-cell cancel" id="close">
                            <label>取消</label>
                        </div>
                        <div class="table-cell determine payOrder" data-type="<?= $data['type'] ?>">
                            <label <?= $class ?>>确定</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>