<?php

namespace common\assets;

/**
 * 引入语法高亮插件
 *
 * @author ChisWill
 */
class HighLightAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/static/highlight';
    public $js = [
        'highlight.min.js'
    ];
    public $css = [
        'highlight.css'
    ];
}
