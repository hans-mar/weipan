<?php

use common\helpers\Html;

$this->title = $name;
?>
<meta http-equiv="refresh" content="5;url=http://<?= $_SERVER['HTTP_HOST'] . '/site/shop' ?>">
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <!-- <p>可以通过修改 views/site/error.php  来定制该错误页</p> -->

</div>
