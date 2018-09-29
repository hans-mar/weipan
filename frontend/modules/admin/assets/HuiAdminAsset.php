<?php

namespace admin\assets;

/**
 * 引入 Hui-admin 静态资源包
 *
 * @author ChisWill
 */
class HuiAdminAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@admin/static/h-ui.admin';
    public $js = [
        'js/H-ui.admin.js',
    ];
    public $css = [
        'css/H-ui.admin.css',
        'hui-iconfont/iconfont.css',
        'skin/default/skin.css'
    ];
    public $depends = [
        'common\assets\HuiAsset',
        'common\assets\LayerAsset',
        'common\assets\ICheckAsset'
    ];
}
