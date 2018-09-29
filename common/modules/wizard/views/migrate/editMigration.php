<?php $form = self::beginForm(['id' => 'migrateForm', 'action' => self::currentUrl()]) ?>
<?= $model->label('description') ?>：<?= $form->field($model, 'description')->textInput(['class' => 'desc-input', 'placeholder' => '简要描述要修改的内容']) ?>
<?= $model->label('inputSql') ?>：<?= $form->field($model, 'inputSql')->textArea(['class' => 'sql-textarea', 'placeholder' => '要更新的SQL语句']) ?>
<div class="migrate-submit-div"><input type="button" class="migrateSubmit" value="修改"></div>
<?php self::endForm() ?>