<?php

namespace admin\assets;

use Yii;
use common\helpers\Html;

/**
 * @author ChisWill
 */
class SettingAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@admin/static/setting';
    public $js = [
        'setting.js'
    ];
    public $css = [
        'setting.css'
    ];
    public $depends = [
        'common\assets\FancyBoxAsset'
    ];

    public function init()
    {
        parent::init();

        $view = Yii::$app->getView();

        $view->on($view::EVENT_END_BODY, function () {
            echo Html::hiddenInput('', self::createUrl(['admin/ajaxRoleInfo']), ['id' => 'ajaxRoleInfoUrl']);
        });
    }
}
