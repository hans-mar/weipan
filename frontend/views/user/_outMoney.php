<?php foreach ($data as $userCharge) :?>
<div style="white-space: nowrap;overflow: hidden;text-overflow:ellipsis;" class="container">
    <div class="list fl">[充值]微信充值</div>
    <div class="list fl" style="color:#000;text-align: center;white-space: nowrap;overflow: hidden;text-overflow:ellipsis;">订单号：<?= $userCharge->trade_no ?></div>
    <div class="lisch fl"><span class="cz">充</span><b> <?= $userCharge->amount ?></b></div>

    <div class="lisch fl" style="color:#afaaaa;width:50%;text-align: center;"><?= $userCharge->created_at ?></div>
    <div class="clearfix" style="clear:both;"></div>
</div>
<?php endforeach ?>
        
        