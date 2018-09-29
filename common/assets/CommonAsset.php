<?php

namespace common\assets;

/**
 * 引入公共js
 *
 * @author ChisWill
 */
class CommonAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/static/ChisWill/js';
    public $js = [
        'common.js'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
