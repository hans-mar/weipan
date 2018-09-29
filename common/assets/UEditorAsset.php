<?php

namespace common\assets;

/**
 * 引入 Ueditor 静态资源
 *
 * @author ChisWill
 */
class UEditorAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@vendor/kucha/ueditor/assets';
    public $js = [
        'ueditor.config.js',
        'ueditor.all.min.js',
    ];
}
