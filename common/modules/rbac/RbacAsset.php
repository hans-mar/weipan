<?php

namespace common\modules\rbac;

/**
 * 权限管理的静态资源
 *
 * @author ChisWill
 */
class RbacAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/modules/rbac/assets';
    public $css = [
        'rbac.css'
    ];
    public $js = [
        'rbac.js'
    ];
    public $depends = [
        'common\assets\JqueryFormAsset',
        'common\assets\LayerAsset',
        'common\assets\FancyBoxAsset'
    ];
}
