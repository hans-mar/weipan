<?php use common\helpers\Html; ?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-param" content="_csrf">
    <meta name="csrf-token" content="S0N1eXBjUDEzJDQ6AwU.Y3InITEmMQgJGgdFLR80ZnJ4GgAyQlE1Vw==">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <title><?= Html::encode($this->title) ?></title>
    <link href="/css/site.css" rel="stylesheet">
    <link href="/css/merge.css" rel="stylesheet">
    <link href="/css/iconfont/wizard.css" rel="stylesheet">
    <!-- <link href="/css/iconfont/wizard.js" rel="stylesheet"> -->
    <script src="/js/jquery.js"></script>
    <!-- <script src="/js/common.js"></script> -->

</head>
<body>
<?php $this->beginBody() ?>

    <?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>