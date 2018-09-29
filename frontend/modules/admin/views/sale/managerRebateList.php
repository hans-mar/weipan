<?php use common\helpers\Html; ?>

<?= $html ?>

<p class="cl pd-5 mt-20">
    <div>返点总额<?= Html::likeSpan($count, ['class' => 'count']) ?>元</div>
</p>