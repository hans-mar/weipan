<?php $this->beginContent('@common/widgets/views/layouts/base.php') ?>

<?php \common\widgets\assets\ResetTableAsset::register($this) ?>

<?php $form = self::beginForm(['id' => 'resetTableForm', 'action' => self::createUrl($action)]) ?>
    <input type="hidden" value="" name="isReset">
    <div class='container'>
        <div class='title'>设置表格所需显示的列</div>
        <div class='content clearfix'>
            <div class='left-area'>
                <div class='left-title'>
                    <?= $leftTitle ?>
                </div>
                <div class='left-content'>
                    <?= $fieldList ?>
                </div>
            </div>
            <div class='center-area'></div>
            <div class='right-area'>
                <div class='right-title'>
                    <?= $rightTitle ?>
                </div>
                <div class='dragSort right-content'>
                    
                </div>
            </div>
        </div>
        <div class='footer'>
            <input type='button' value='重置' class='reset-button'>
            <input type='button' value='确认' class='submit-button'>
        </div>
    </div>
<?php self::endForm() ?>

<input type='hidden' value='<?= $this->context->maxColumnNum ?>' id='maxColumnNum'>
<input type='hidden' value='<?= $setFieldList ?>' id='setFieldList'>

<?php $this->endContent(); ?>