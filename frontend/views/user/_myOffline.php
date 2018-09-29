<?php use frontend\models\User; ?>
<?php foreach ($data as $user) :?>
<div class="row content">
    <div class="col-xs-4 list"><span><?= $user->mobile ?></span></div>
    <div class="col-xs-4 list"><span>￥<?= User::getUserChargeAmount($user->id) ?></span></div>
    <div class="col-xs-4 list"><span>￥<?= User::getUserRebateAmount($user->id) ?></span></div>
</div>
<?php endforeach ?>