<?php

namespace common\assets;

/**
 * 引入 HighCharts 插件
 *
 * @author ChisWill
 */
class HighChartsAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/highcharts';
    public $js = [
        'highcharts.js'
    ];
}
