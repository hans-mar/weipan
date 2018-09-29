<?php

namespace common\assets;

/**
 * 引入 Cropper 插件
 *
 * @author ChisWill
 */
class CropperAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/cropper/dist';
    public $js = [
        'cropper.min.js'
    ];
    public $css = [
        'cropper.min.css'
    ];
    public $depends = [
        'common\assets\CommonAsset'
    ];
}
