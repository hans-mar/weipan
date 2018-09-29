<?php

namespace common\assets;

/**
 * 引入 HighStock 插件
 *
 * @author ChisWill
 */
class HighStockAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/highcharts';
    public $js = [
        'highstock.js'
    ];
}
