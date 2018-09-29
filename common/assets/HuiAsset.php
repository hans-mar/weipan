<?php

namespace common\assets;

/**
 * 引入 H-ui 静态资源包
 */
class HuiAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/static/h-ui';
    public $js = [
        'js/H-ui.js'
    ];
    public $css = [
        'css/H-ui.min.css'
    ];
    public $depends = [
        'common\assets\IEAsset',
        'yii\web\JqueryAsset'
    ];
}
