<?php

namespace common\assets;

/**
 * 引入 Jquery-form 插件
 *
 * @author ChisWill
 */
class JqueryFormAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/jquery-form';
    public $js = [
        'jquery.form.js'
    ];
    public $depends = [
        'common\assets\CommonAsset'
    ];
}
