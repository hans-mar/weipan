<?php $form = self::beginForm(['id' => 'migrateForm']) ?>
<?php if ($model->commitUser !== null): ?>
<?= $model->label('commitUser') ?>：<?= $model->commitUser . $form->field($model, 'commitUser')->hiddenInput() ?>
<?php else: ?>
<?= $model->label('commitUser') ?>：<?= $form->field($model, 'commitUser')->textInput(['placeholder' => '必须填写真实姓名']) ?>
<?php endif ?>
<?= $model->label('description') ?>：<?= $form->field($model, 'description')->textInput(['class' => 'desc-input', 'placeholder' => '简要描述要修改的内容']) ?>
<?= $model->label('inputSql') ?>：<?= $form->field($model, 'inputSql')->textArea(['class' => 'sql-textarea', 'placeholder' => '要更新的SQL语句']) ?>
<div class="migrate-submit-div"><input type="button" class="migrateSubmit" value="创建"></div>
<?php self::endForm() ?>