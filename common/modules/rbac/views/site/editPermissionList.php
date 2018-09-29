<?php use common\helpers\Html; ?>
<?php use common\helpers\Inflector; ?>

<?= $this->render('_tabMenu', compact('tabMenu')) ?>

<?php $form = self::beginForm() ?>
<table>
    <tr>
        <th>action</th>
        <th>描述</th>
        <th>规则</th>
    </tr>
    <?php $controllerName = '' ?>
    <?php foreach ($authItems as $index => $item): ?>
    <?php 
        $name = Inflector::camel2id($item->name);
        $namePieces = explode('-', $name);
        if ($controllerName != $namePieces[1]) {
            $controllerName = $namePieces[1];
            echo '
    <tr>
        <th colspan=3>' . $controllerName . '</th>
    </tr>
            ';
        }
        unset($namePieces[0], $namePieces[1]);
        $showName = lcfirst(Inflector::id2camel(implode('-', $namePieces)));
    ?>
    <tr>
        <td>
            <?= $showName ?>
            <?= $form->field($item, "[$index]type", ['inputOptions' => ['readOnly' => 'readOnly']])->hiddenInput() ?>
            <?= $form->field($item, "[$index]name", ['inputOptions' => ['readOnly' => 'readOnly']])->hiddenInput() ?>
        </td>
        <td><?= $form->field($item, "[$index]description")->textInput(['class' => 'permission-input']) ?></td>
        <td><?= $form->field($item, "[$index]rule_name")->textInput(['class' => 'rule-input', 'placeholder' => '规则名或是规则类名']) ?></td>
    </tr>
    <?php endforeach ?>
</table>
<?= Html::submitInput('修改') ?>
<?php self::endForm() ?>