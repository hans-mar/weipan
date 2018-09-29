<?php use common\helpers\Html; ?>
<?php use common\helpers\Inflector; ?>

<?= $this->render('_tabMenu', compact('tabMenu')) ?>

<?php if ($models): ?>
<?php $form = self::beginForm() ?>
<table>
    <tr>
        <th>序号</th>
        <th>action</th>
        <th>描述</th>
        <th>规则</th>
    </tr>
    <?php foreach ($models as $index => $model): ?>
    <?php
    $namePieces = explode('-', Inflector::camel2id($model->name));
    unset($namePieces[0]);
    $actionName = array_shift($namePieces) . '->' . ($action = lcfirst(Inflector::id2camel(implode('-', $namePieces))));
    $model->description or $model->description = $action;
    ?>
    <tr>
        <td>
            <?= ($index + 1) ?>
            <?= $form->field($model, "[$index]type")->hiddenInput(['readOnly' => 'readOnly']) ?>
        </td>
        <td>
            <?= $actionName ?>
            <?= $form->field($model, "[$index]name")->hiddenInput(['readOnly' => 'readOnly']) ?>
        </td>
        <td><?= $form->field($model, "[$index]description")->textInput(['class' => 'permission-input']) ?></td>
        <td><?= $form->field($model, "[$index]rule_name")->textInput(['class' => 'rule-input', 'placeholder' => '规则名或是规则类名']) ?></td>
    </tr>
    <?php endforeach ?>
</table>
<?= Html::submitInput('创建') ?>
<?php self::endForm() ?>
<?php else: ?>
<div class="empty-info">暂无待创建的权限~</div>
<?php endif ?>