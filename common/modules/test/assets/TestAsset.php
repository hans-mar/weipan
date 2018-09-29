<?php

namespace common\modules\test\assets;

/**
 * 测试模块的静态资源
 *
 * @author ChisWill
 */
class TestAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/modules/test/static';
    public $css = [
        'test.css'
    ];
    public $js = [
        'test.js'
    ];
    public $depends = [
        'common\assets\CommonAsset',
        'common\assets\FancyBoxAsset',
        // 'yii\bootstrap\BootstrapAsset'
    ];
}
