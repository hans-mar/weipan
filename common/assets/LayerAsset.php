<?php

namespace common\assets;

use Yii;

/**
 * 引入 Layer 插件
 *
 * @author ChisWill
 */
class LayerAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/layer';
    public $js = [
        'layer.js',
    ];
    public $depends = [
        'common\assets\CommonAsset'
    ];
}
