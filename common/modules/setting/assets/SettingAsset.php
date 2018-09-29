<?php

namespace common\modules\setting\assets;

/**
 * 公共设定的静态资源
 *
 * @author ChisWill
 */
class SettingAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/modules/setting/static';
    public $css = [
        'setting.css'
    ];
    public $js = [
        'setting.js'
    ];
    public $depends = [
        'common\assets\JqueryFormAsset',
        'common\assets\LayerAsset'
    ];
}
