<?php

namespace common\modules\wizard\assets;

/**
 * 代码生成工具栏的静态资源
 *
 * @author ChisWill
 */
class WizardAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/modules/wizard/static';
    public $css = [
        'wizard.css'
    ];
    public $js = [
        'wizard.js'
    ];
    public $depends = [
        'common\assets\JqueryFormAsset',
        'common\assets\FancyBoxAsset'
    ];
}
