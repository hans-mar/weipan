<?php $form = self::beginForm(['showLabel' => true]) ?>
<?= $form->field($model, 'username') ?>
<?= $form->field($model, 'password')->passwordInput() ?>
<?= $form->field($model, 'captcha')->captcha() ?>
<input type="submit" value="注册">
<?php self::endForm() ?>