<?php foreach ($data as $userWithdraw) :?>
<div class="container">
    <div class="list fl">[提现]</div>
    <div class="list fl" style="color:#000;text-align: center;">状态：<?= $userWithdraw->getOpStateValue($userWithdraw->op_state) ?></div>
    <div class="lisch fl"><span class="cz">提</span><b> <?= $userWithdraw->amount ?></b></div>
    <div class="lisch fl"><span class="fy">费</span> 4</div>
    <div class="lisch fl" style="color:#afaaaa;width:50%;text-align: center;"><?= $userWithdraw->created_at ?></div>
    <div class="clearfix" style="clear:both;"></div>
</div>
<?php endforeach ?>