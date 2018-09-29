<?php

namespace common\assets;

use Yii;

/**
 * 引入 fancyBox 插件
 *
 * @author ChisWill
 */
class FancyBoxAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/fancybox/source';
    public $js = [
        'jquery.fancybox.js'
    ];
    public $css = [
    	'jquery.fancybox.css'
    ];
    public $depends = [
        'common\assets\CommonAsset'
    ];

    public function init()
    {
        parent::init();

        $view = Yii::$app->getView();

        $view->registerJs('$.listen("fancybox");');
    }
}
