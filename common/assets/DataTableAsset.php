<?php

namespace common\assets;

/**
 * 引入 datatables 组件
 *
 * @author ChisWill
 */
class DataTableAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/static/datatables';
    public $js = [
        'datatables.js',
        'https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js'
    ];
    public $css = [
        'https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.css'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
