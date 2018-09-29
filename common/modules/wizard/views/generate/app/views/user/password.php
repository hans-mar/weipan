<?php $form = self::beginForm(['showLabel' => true]) ?>
<?= $form->field($model, 'oldPassword')->passwordInput() ?>
<?= $form->field($model, 'newPassword')->passwordInput() ?>
<?= $form->field($model, 'cfmPassword')->passwordInput() ?>
<input type="submit" value="修改密码">
<?php self::endForm() ?>