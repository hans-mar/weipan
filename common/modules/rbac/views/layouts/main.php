<?php use common\helpers\Html; ?>
<?php common\modules\rbac\assets\RbacAsset::register($this) ?>
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

<ul class="rbac-nav clearfix">
    <li <?php if (strpos($this->context->action->id, 'permission') !== false): ?>class="selected"<?php endif ?>><a href="<?= self::createUrl(['site/editPermissionList']) ?>">权限列表</a></li>
    <li <?php if (strpos($this->context->action->id, 'role') !== false): ?>class="selected"<?php endif ?>><a href="<?= self::createUrl(['site/roleList']) ?>">角色列表</a></li>
    <li <?php if ($this->context->action->id === 'user-list'): ?>class="selected"<?php endif ?>><a href="<?= self::createUrl(['site/userList']) ?>">用户列表</a></li>
</ul>

    <?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>