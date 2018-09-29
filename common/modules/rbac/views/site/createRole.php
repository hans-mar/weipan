<?php use common\helpers\Html; ?>

<?= $this->render('_tabMenu', compact('tabMenu')) ?>

<?php $form = self::beginForm() ?>
<table>
    <tr>
        <th class="required">角色名</th>
        <td><?= $form->field($model, 'name') ?></td>
    </tr>
    <tr>
        <th>规则名</th>
        <td><?= $form->field($model, 'rule_name')->textInput(['placeholder' => '规则名或是规则类名']) ?></td>
    </tr>
    <?php if ($roles): ?>
    <tr>
        <th>当前所有的角色</th>
        <td><?= $form->field($model, 'roles')->checkboxList($roles) ?></td>
    </tr>
    <?php endif ?>
    <tr>
        <th>当前所有的权限</th>
        <td>
        <?php foreach ($permissions as $groupName => $mapData): ?>
            <?= Html::successSpan($groupName) ?>
            <?= $form->field($model, 'permissions')->checkboxList($mapData) ?>
        <?php endforeach ?>
        </td>
    </tr>
</table>
<?= Html::submitInput('确认') ?>
<?php self::endForm() ?>

<script>
$(function () {
    // 删除权限多选框多余的默认隐藏域（Yii的checkboxList方法每执行一次都会生成一个默认的隐藏域，用来当做checkbox的默认值）
    $("input[type='hidden'][name*='AuthItem[permissions]']:gt(0)").remove();
});
</script>