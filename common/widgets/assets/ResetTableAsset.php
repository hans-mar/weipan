<?php

namespace common\widgets\assets;

/**
 * Table 组件的列重置界面的静态资源包
 *
 * @author ChisWill
 */
class ResetTableAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/widgets/static';
    public $js = [
        'js/resetTable.js',
    ];
    public $css = [
        'css/resetTable.css'
    ];
    public $depends = [
        'common\assets\JqueryFormAsset',
        'common\assets\SortableAsset',
        'common\assets\LayerAsset'
    ];
}
