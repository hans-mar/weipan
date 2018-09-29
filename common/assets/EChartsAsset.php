<?php

namespace common\assets;

/**
 * 引入 ECharts 插件
 *
 * @author ChisWill
 */
class EChartsAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/echarts/dist';
    public $js = [
        'echarts.min.js',
    ];
}
