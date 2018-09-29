<?php $form = self::beginForm() ?>
<?= $form->field($model, 'captcha')->captcha() ?>
<input type="submit" value="提交">
<?php self::endForm() ?>