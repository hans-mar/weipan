<?php

namespace common\assets;

/**
 * 兼容IE9以下版本的资源包
 */
class IEAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/static/ie';
    public $js = [
        'html5.js',
        'respond.min.js',
        'PIE_IE678.js'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD,
        'condition' => 'lt IE 9'
    ];
}
