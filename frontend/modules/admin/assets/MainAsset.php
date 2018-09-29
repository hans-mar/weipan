<?php

namespace admin\assets;

/**
 * @author ChisWill
 */
class MainAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@admin/static/main';
    public $js = [
        'main.js'
    ];
    public $css = [
        'main.css'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
    public $depends = [
        'common\assets\CommonAsset',
        'admin\assets\HuiAdminAsset',
        'common\assets\FancyBoxAsset'
    ];
}
