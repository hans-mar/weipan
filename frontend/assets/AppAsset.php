<?php

namespace frontend\assets;

/**
 * frontend 基础静态资源
 */
class AppAsset extends \common\components\AssetBundle
{
    public $js = [
        'js/site.js',
        'js/main.js',
        // 'css/bootstrap/js/bootstrap.min.js'
    ];
    public $css = [
        'css/site.css',
        'css/main.css',
        'css/layer.css',
        // 'css/bootstrap/css/bootstrap.min.css',
        'css/common.css'
    ];
    public $depends = [
        'common\assets\CommonAsset',
        'common\assets\JqueryFormAsset',
        'common\assets\LayerAsset'
    ];
}
