<?php $this->regCss('manager.css') ?>

<div id="main">
    <div class="querylist-box" style="visibility: visible;">
        <div class="queryheader-wrap">
            <div class="queryheader boxflex">
                <div class="box_flex_1" style="width: 45%;">
                    <i class="icon icon-hold"></i>
                    <span class="headervalue">总手数:
                    <span class="redsymbol coverNum"><?= $order->hand ?>手</span>
                    </span>
                </div>
                <div class="box_flex_1" style="width: 55%;">
                    <i class="icon icon-profit"></i>
                    <span class="headervalue">总手续费:
                    <span class="redsymbol coverEarn earn-zern"><?= $order->fee ?></span>
                    </span>
                </div>
            </div>
            <?php $form = self::beginForm(['method' => 'get']) ?>
            <div class="condition">
                <div class="boxflex time">
                    <div class="key">日期:
                    </div>
                    <div class="box_flex_1 value">
                        <?= $form->field($model, 'start_date')->textInput(['type' => 'date', 'class' => 'laydate-icon', 'placeholder' => '请输入起始日期']) ?>
                    </div>
                </div>
                <div class="boxflex value time">
                    <div class="key">至:
                    </div>
                    <div class="box_flex_1 value">
                        <?= $form->field($model, 'end_date')->textInput(['type' => 'date', 'class' => 'laydate-icon', 'placeholder' => '请输入结束日期']) ?>
                    </div>
                </div>
                <div class="boxflex goodlist">
                    <div class="key">商品:
                    </div>
                    <div class="box_flex_1 value">
                        <?= $form->field($model, 'product_id')->dropDownList($productArr, ['class' => 'coverSelect', 'placeholder' => '请选择商品', 'prompt' => '全部']) ?>
                    </div>
                </div>
            </div>
            <div class="btn-queryheader">
                <div class="btn-query"><button class="btn btn-45-24-blue" id="searchBtn" type="submit">查询</button>
                </div>
            </div>
            <?php self::endForm() ?>
        </div>
        <div class="listwrap">

            <div class="boxflex header">
                <div class="box_flex_1 name">客户昵称
                </div>
                <div class="box_flex_1 good">商品
                </div>
                <div class="box_flex_1 balance">手续费
                </div>
                <div class="box_flex_1 time">平仓时间
                </div>
            </div>

            <?php foreach ($data as $order): ?>
            <div class="boxflex header list">
                <div class="name box_flex_1"><?= $order->user->nickname ?>
                </div>
                <div class="phone box_flex_1"><?= $order->product->name ?>
                </div>
                <div class="balance box_flex_1"><?= $order->fee ?>
                </div>
                <div class="time box_flex_1"><?= $order->updated_at ?>
                </div>
            </div>
            <?php endforeach ?>

            <?= self::linkPager() ?>
            <div class="iscroll-wrap" style="height: 382px;">
                <div class="iscroll-content" style="transition-timing-function: cubic-bezier(0.1, 0.57, 0.1, 1); transition-duration: 0ms; transform: translate(0px, 0px) translateZ(0px); min-height: 383px;">
                    <ul style="min-height: 383px;">
                        <li class="data-empty">当前查询记录为<?= $count ?>条</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

  