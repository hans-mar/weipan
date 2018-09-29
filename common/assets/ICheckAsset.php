<?php

namespace common\assets;

use Yii;

/**
 * 引入 iCheck 插件
 *
 * @author ChisWill
 */
class ICheckAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/iCheck';
    public $js = [
        'icheck.min.js'
    ];
    public $css = [
        'skins/minimal/_all.css'
    ];
    public $depends = [
        'common\assets\CommonAsset'
    ];
}
