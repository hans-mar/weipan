<?php

namespace common\assets;

use Yii;

/**
 * 引入 My97DatePicker 插件
 *
 * @author ChisWill
 */
class DatePickerAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@common/static/My97DatePicker';
    public $js = [
        'WdatePicker.js'
    ];
}
