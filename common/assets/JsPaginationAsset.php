<?php

namespace common\assets;

/**
 * 引入自定义的表格分页、排序组件
 *
 * @author ChisWill
 */
class JsPaginationAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/static/ChisWill';
    public $js = [
        'js/pagination.js'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
    public $css = [
        'css/pagination.css'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
