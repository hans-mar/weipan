<?php

namespace common\modules\wizard\assets;

/**
 * 数据迁移页面的静态资源
 *
 * @author ChisWill
 */
class MigrateAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/modules/wizard/static';
    public $css = [
        'migrate.css'
    ];
    public $js = [
        'migrate.js'
    ];
    public $depends = [
        'common\assets\CommonAsset',
        'common\assets\JsPaginationAsset',
        'common\assets\JqueryFormAsset',
        'common\assets\FancyBoxAsset',
        'common\assets\LayerAsset'
    ];
}
