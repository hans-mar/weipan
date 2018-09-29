<?php $form = self::beginForm(['showLabel' => true]) ?>
<?= $form->field($model, 'username') ?>
<?= $form->field($model, 'password')->passwordInput() ?>
<input type="submit" value="登录">
<?php self::endForm() ?>