<?php use common\helpers\Html; ?>
<?php common\modules\wizard\assets\MigrateAsset::register($this) ?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <ul>
        <?php foreach ($this->context->menu as $menu): ?>
        <li><?= Html::a($menu['name'], [$menu['url']], ['class' => $this->context->action->id === $menu['url'] ? 'active' : '']) ?></li>
        <?php endforeach ?>
    </ul>

    <?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>