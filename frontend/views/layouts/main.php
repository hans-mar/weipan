<?php use common\helpers\Html; ?>
<?php frontend\assets\AppAsset::register($this) ?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="HandheldFriendly" content="true">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <?= $content ?>
<!--<ul class="clear-fl footer-nav">
    <li class="<?= $this->context->id=='site'&&$this->context->module->requestedRoute!='site/index'?'active':'' ?>"><a href="<?= url('site/shop') ?>">
        商城
    </a></li>
    <li class="<?= $this->context->module->requestedRoute=='site/index'?'active':'' ?>"><a href="<?= url('site/index') ?>">
        交易
    </a></li>
    <li class="<?= in_array($this->context->id, ['user', 'manager'])?'active':'' ?>"><a href="<?= url('user/index') ?>">
        我的
    </a></li>
    <li class="<?= in_array($this->context->id, ['user', 'manager'])?'active':'' ?>"><a href="<?= url('user/index') ?>">
        我的
    </a></li>
</ul>
<input type="hidden" value="<?= url(['site/getData']) ?>" id="getStockDataUrl">-->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>