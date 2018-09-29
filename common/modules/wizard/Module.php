<?php

namespace common\modules\wizard;

use Yii;
use common\components\ARModel;
use common\helpers\FileHelper;

/**
 * 代码生成工具栏
 * 其中的数据库迁移部分，默认了 windows 环境为本地开发环境，数据库迁移的创建、删除等功能只能在开发环境中进行
 * 
 * @author ChisWill
 */
class Module extends \common\components\Module
{
    /**
     * @var array|Generator[] a list of generator configurations or instances. The array keys
     * are the generator IDs (e.g. "crud"), and the array elements are the corresponding generator
     * configurations or the instances.
     *
     * After the module is initialized, this property will become an array of generator instances
     * which are created based on the configurations previously taken by this property.
     */
    public $generators = [
        'model' => ['class' => 'common\modules\wizard\generators\model\Generator'],
        'field' => ['class' => 'common\modules\wizard\generators\field\Generator'],
        'migrate' => ['class' => 'common\modules\wizard\generators\migrate\Generator'],
        'module' => ['class' => 'common\modules\wizard\generators\module\Generator'],
        'app' => ['class' => 'common\modules\wizard\generators\app\Generator']
    ];
    /**
     * 该模块在开发环境时不要求登录
     */
    public $loginRequired = YII_ENV_PROD;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\web\Application) {

            $app->getUrlManager()->addRules([
                $this->id => $this->id . '/migrate/history-list',
                $this->id . '/?' => $this->id . '/migrate/history-list'
            ], false);

            if (!YII_ENV_PROD) {
                $app->on($app::EVENT_BEFORE_REQUEST, function () use ($app) {
                    $view = $app->getView();
                    $view->on($view::EVENT_END_BODY, [$this, 'renderWizardBar']);
                });
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->resetGlobalSettings();

        return true;
    }

    /**
     * Resets potentially incompatible global settings done in app config.
     */
    protected function resetGlobalSettings()
    {
        if (Yii::$app instanceof \yii\web\Application) {
            Yii::$app->assetManager->bundles = [];
        }
    }

    /**
     * Renders mini-toolbar at the left of page body.
     *
     * @param \yii\base\Event $event
     */
    public function renderWizardBar($event)
    {
        $view = $event->sender;
        // 获取当前项目与公共项目
        $apps = ['common', FileHelper::getCurrentApp()];
        $extendItems = [ARModel::STATE_VALID => '继承公共', ARModel::STATE_INVALID => '不继承'];
        
        echo $view->render('@common/modules/wizard/views/generate/index', compact('apps', 'extendItems'));
    }
}
