<?php use common\helpers\Hui; ?>
<?php use common\helpers\ArrayHelper; ?>
<?php admin\assets\RoleAsset::register($this) ?>
<?php $categoryMap = admin\models\AdminMenu::categoryMap() ?>

<?php $form = self::beginForm() ?>
<?= Hui::h2($title, ['class' => 'text-c']) ?>
<?= $form->field($model, 'name')->label('角色名') ?>
<?= $form->field($model, 'rule_name')->label('规则名')->textInput(['placeholder' => '规则名或是规则类名']) ?>
<?php if ($roles): ?>
<?= $form->field($model, 'roles')->label('当前所有的角色')->checkboxList($roles) ?>
<?php endif ?>
<?= Hui::primaryH4('以下是当前所有的权限', ['class' => 'text-c']) ?>
<?php foreach ($permissions as $groupName => $mapData): ?>
<?= $form->field($model, 'permissions')->label(ArrayHelper::getValue($categoryMap, $groupName, '常规'))->checkboxList($mapData) ?>
<?php endforeach ?>
<?= $form->submit() ?>
<?php self::endForm() ?>

<script>
$(function () {
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '操作成功', function () {
                        parent.location.reload();
                    });
                } else {
                    $.alert(msg.info);
                }
            }
        }));
        return false;
    });
    // 删除权限多选框多余的默认隐藏域（Yii的checkboxList方法每执行一次都会生成一个默认的隐藏域，用来当做checkbox的默认值）
    $("input[type='hidden'][name*='AuthItem[permissions]']:gt(0)").remove();
});
</script>