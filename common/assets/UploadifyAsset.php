<?php

namespace common\assets;

/**
 * 引入 uploadify 静态资源
 *
 * @author ChisWill
 */
class UploadifyAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/uploadify';
    public $js = [
        'jquery.uploadify.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
