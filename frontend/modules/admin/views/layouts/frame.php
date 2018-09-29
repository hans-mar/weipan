<?php use common\helpers\Html; ?>
<?php admin\assets\FrameAsset::register($this) ?>
<?php self::offEvent(['wizard']) ?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <nav class="breadcrumb">
        <i class="Hui-iconfont">&#xe67f;</i> 首页 
        <span class="c-gray en">&gt;</span> <?= Html::span('', ['id' => 'breadcrumbMain']) ?> 
        <span class="c-gray en">&gt;</span> <?= Html::span('', ['id' => 'breadcrumbSub']) ?> 
        <a class="btn btn-success radius r" style="margin-top: 3px;" href="javascript:location.replace(location.href);" title="刷新" >
            <i class="Hui-iconfont">&#xe68f;</i>
        </a>
    </nav>

    <div class="page-container">
        <?= $content ?>
    </div>

<script>
$(function () {
    // 面包屑自动处理
    ;!function () {
        var index = $.parent("#min_title_list li.active").index(),
            $iframe = $.parent("iframe:eq(" + index + ")");
        if ($iframe.data("subtitle")) {
            if ($iframe.data("maintitle") !== 'undefined') {
                $("#breadcrumbMain").html($iframe.data("maintitle"));
            } else {
                $("#breadcrumbMain").prev().hide();
            }
            $("#breadcrumbSub").html($iframe.data("subtitle"));
        } else {
            $("nav.breadcrumb").hide();
        }
    }();
});
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>