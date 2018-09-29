<?php

namespace common\assets;

/**
 * 引入 jquery-ui 组件
 *
 * @author ChisWill
 */
class JqueryUiAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/jquery-ui';
    public $js = [
        'jquery-ui.min.js'
    ];
    public $css = [
   	    'themes/smoothness/jquery-ui.min.css'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
