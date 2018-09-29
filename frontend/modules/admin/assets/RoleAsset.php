<?php

namespace admin\assets;

use Yii;
use common\helpers\Html;

/**
 * @author ChisWill
 */
class RoleAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@admin/static/role';
    public $js = [
        'role.js'
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
