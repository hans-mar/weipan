<?php use common\helpers\Hui; 
use admin\models\AdminUser;?>

<?php $form = self::beginForm() ?>
<?php if (u()->power == AdminUser::POWER_MANAGER): ?>
<?= $form->field($model, 'realname')->textInput(['disabled' => 'disabled']) ?>
<?= $form->field($model, 'company_name')->textInput(['disabled' => 'disabled']) ?>
<?= $form->field($model, 'tel')->textInput(['disabled' => 'disabled']) ?>
<?= $form->field($model, 'total_fee')->textInput(['disabled' => 'disabled']) ?>
<?= $form->field($model, 'code')->textInput(['disabled' => 'disabled']) ?>
<?php else: ?>
<?= $form->field($model, 'mobile')->textInput(['disabled' => 'disabled']) ?>
<?php $text = '保证金';if (u()->power == AdminUser::POWER_ADMIN){$text = '返点总额';}?>
<?= $form->field($model, 'deposit')->textInput(['disabled' => 'disabled'])->label($text) ?>
<?php endif ?>
<?= $form->field($model, 'point')->textInput(['disabled' => 'disabled']) ?>
<?php self::endForm() ?>