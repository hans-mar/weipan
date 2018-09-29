<?php

namespace common\widgets\assets;

/**
 * Table 组件的静态资源包
 *
 * @author ChisWill
 */
class TableAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/widgets/static';
    public $js = [
        'js/table.js'
    ];
    public $css = [
        'css/table.css'
    ];
    public $depends = [
        'common\assets\CommonAsset',
        'common\assets\JqueryFormAsset',
        'common\assets\TimePickerAsset',
        'common\assets\FancyBoxAsset',
        'common\assets\LayerAsset'
    ];
}
