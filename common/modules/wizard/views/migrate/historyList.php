<?php use common\helpers\Html; ?>
<?php \common\assets\HighLightAsset::register($this) ?>

<?php if (empty($files)): ?>
<?= Html::warningSpan('还没有提交过数据库迁移记录') ?>
<?php else: ?>
<div id="sync-all-div">
    <div>
        <?= Html::a(Html::button('同步所有'), ['generate/code'], ['id' => 'sync-all']) ?>
    </div>
    <div class="search-form-div">
        <input type="text" placeholder="请输入要搜索的内容..." id="searchInput">
        <input type="submit" value="搜索" id="searchSubmit">
    </div>
</div>

<div id="ajax-area">
    <?= $this->render('_historyList', compact('files', 'history', 'model', 'key', 'user')) ?>
</div>
<?php endif ?>