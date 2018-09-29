<?php

namespace common\assets;

use Yii;

/**
 * 引入 socket.io 静态资源
 *
 * @author ChisWill
 */
class SocketIOAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@vendor/node_modules/socket.io/node_modules/socket.io-client';
    public $js = [
        'socket.io.js',
    ];
    public $depends = [
        'common\assets\CommonAsset'
    ];

    public function init()
    {
        parent::init();

        $view = Yii::$app->getView();

        $socketPort = Yii::$app->params['workermanSocketIOPort'];
        $webDomain = Yii::$app->params['webDomain'];
        $url = $webDomain . ':' . $socketPort;

        $view->registerJs('var _config = _config || {};_config["socketioUrl"] = "' . $url . '";', $view::POS_HEAD);
    }
}
