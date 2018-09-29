<?php use common\helpers\Html; ?>

<section class="container-fluid page-404 minWP text-c">
    <p class="error-title">
        <i class="Hui-iconfont va-m" style="font-size:80px">&#xe688;</i>
    </p>
    <p class="error-description"><?= str_repeat('&nbsp;', 2) . nl2br(Html::encode($message)) ?></p>
</section>