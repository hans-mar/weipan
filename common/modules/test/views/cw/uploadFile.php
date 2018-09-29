<?php $form = self::beginForm() ?>
<?= $form->field($model, 'uid') ?>
<?= $form->field($model, 'message')->markdown() ?>
<?php //echo $form->field($model, 'file[]')->fileInput(['accept' => 'image/*']) ?>
<?php echo $form->field($model, 'file[]')->fileInput(['accept' => 'image/*']) ?>
<?php echo $form->field($model, 'file[]')->fileInput(['accept' => 'image/*']) ?>

<input type="submit" value="提交">
<?php self::endForm() ?>