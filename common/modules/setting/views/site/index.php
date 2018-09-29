<?php $baseUrl = $this->getAssetUrl('common\modules\setting\assets\SettingAsset') ?>

<div class="setting-container">
    <ul class="clearfix" id="topParentUl">
        <?php
        $nowTopId = '';
        $selected = 'selected';
        foreach ($settings as $setting) {
            if ($setting['pid'] == 0) {
                $nowTopId = $nowTopId ?: $setting['id'];
                echo '
                    <li>
                        <a href="javascript:;" class="topMenuList ' . $selected . '" data-id="' . $setting['id'] . '">
                        ' . $setting['name'] . '
                        </a>
                    </li>';
                $selected = '';
            }
        }
        ?>
        <li id="addTopParentLi" class="showMode">
            <img src="<?= $baseUrl ?>/add.png" id="addTopParentLink" href="<?= self::createUrl('addSetting') ?>">
        </li>
        <?php if (YII_ENV_DEV): ?>
        <li class="change-edit-li">
            <a href="javascript:;" id="changeEditBtn" mode="1">切换到编辑模式</a>
        </li>
        <?php endif ?>
    </ul>
    
    <div id="webSettingContent">
        <?= $this->render('_webSetting', compact('settings', 'nowTopId')) ?>
        <div style="display:none;" id="settingData"><?= json_encode($settings) ?></div>
    </div>
</div>