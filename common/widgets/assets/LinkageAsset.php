<?php

namespace common\widgets\assets;

/**
 * Linkage 组件的静态资源包
 *
 * @author ChisWill
 */
class LinkageAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/widgets/static';
    public $js = [
        'js/linkage.js'
    ];
    public $css = [
        'css/linkage.css'
    ];
}
