<?php

namespace common\assets;

use Yii;

/**
 * 引入 Sortable 插件
 *
 * @author ChisWill
 */
class SortableAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@bower/Sortable';
    public $js = [
        'Sortable.min.js'
    ];
    public $depends = [
        'common\assets\CommonAsset'
    ];

    public function init()
    {
        parent::init();

        $view = Yii::$app->getView();

        $view->registerJs('$.listen("dragSort");');
        $view->registerJs('$.listen("groupSort");');
    }
}
