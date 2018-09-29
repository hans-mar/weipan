<?php

namespace common\assets;

use Yii;

/**
 * 引入 Jquery-ui-timepicker 插件
 *
 * @author ChisWill
 */
class TimePickerAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/jqueryui-timepicker-addon/dist';
    public $js = [
        'jquery-ui-timepicker-addon.min.js',
        'jquery.ui.datepicker-zh-CN.js.js',
        'jquery-ui-timepicker-zh-CN.js'
    ];
    public $css = [
    	'jquery-ui-timepicker-addon.min.css'
    ];
    public $depends = [
        'common\assets\JqueryUiAsset',
        'common\assets\CommonAsset'
    ];

    public function init()
    {
        parent::init();

        $view = Yii::$app->getView();

        $view->registerJs('$.listen("datetimepicker");');
        $view->registerJs('$.listen("datepicker");');
        $view->registerJs('$.listen("timepicker");');
        $view->registerJs('$.listen("dateRange", "startdate");');
        $view->registerJs('$.listen("timeRange", "starttime");');
    }
}
