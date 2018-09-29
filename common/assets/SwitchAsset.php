<?php

namespace common\assets;

/**
 * 引入 bootstrapSwitch 插件
 *
 * @author ChisWill
 */
class SwitchAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/static/bootstrapSwitch';
    public $js = [
        'bootstrapSwitch.js'
    ];
    public $css = [
        'bootstrapSwitch.css'
    ];
}
