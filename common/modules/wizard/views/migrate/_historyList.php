<?php use common\helpers\Html; ?>
<table id="migration-history-table">
    <tr>
        <th width="10%">序号</th>
        <th>描述</th>
        <th width="10%">提交者</th>
        <th width="15%">提交时间</th>
        <th width="10%">状态</th>
        <th width="15%">操作</th>
    </tr>
    <?php
    foreach ($files as $index => $file): 
        if ($model->dumpInfo($file, 'delete')) {
            continue;
        } elseif ($key) {
            if (strpos($model->dumpInfo($file, 'sql'), $key) === false) {
                continue;
            }
        }
        $fileName = basename($file);
    ?>
    <tr title="<?= $fileName ?>">
        <td><?= $index + 1 ?></td>
        <td><?= Html::a($model->dumpInfo($file, 'desc'), ['info', 'file' => $fileName], ['class' => 'view-fancybox fancybox.ajax']) . $model->dumpInfo($file, 'warning') ?></td>
        <td><?= $model->dumpInfo($file, 'user') ?></td>
        <td><?= date('Y-m-d H:i:s', filectime($file)) ?></td>
        <td><?= array_key_exists($fileName, $history) ? Html::successSpan('已同步') : Html::errorSpan('未同步') ?></td>
        <td data-desc="<?= $model->dumpInfo($file, 'desc') ?>">
            <?= Html::a('查看', ['info', 'file' => $fileName], ['class' => 'view-fancybox fancybox.ajax']) ?>
            <?php if (!array_key_exists($fileName, $history)): ?>
                <?= Html::a('同步', ['sync', 'file' => $fileName], ['class' => 'sync-migration']) ?>
            <?php endif ?>
            <?php if ($this->context->isLocalEnv()): ?>
                <?php if ($user === $model->dumpInfo($file, 'user')): ?>
            <?= Html::a('修改', ['edit', 'file' => $fileName], ['class' => 'edit-migration edit-fancybox fancybox.ajax']) ?>
                <?php endif ?>
            <?= Html::a('删除', ['deleteVersion', 'file' => $fileName], ['class' => 'delete-migration']) ?>
            <?php endif ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>