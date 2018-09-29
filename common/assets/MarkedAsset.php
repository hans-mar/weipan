<?php

namespace common\assets;

/**
 * 引入 markdown 插件
 *
 * @author ChisWill
 */
class MarkedAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/marked';
    public $js = [
        'marked.min.js'
    ];
    public $depends = [
        'common\assets\HighLightAsset'
    ];
}
