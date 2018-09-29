<?php

namespace admin;

use Yii;
use common\helpers\Html;

/**
 * 后台模块启动文件
 */
class Module extends \common\components\Module
{
    public function bootstrap($app)
    {
        $isAdminHost = strpos($app->getUrlManager()->hostinfo, 'http://admin.') === 0;
        // 动态定制路由
        if ($app instanceof \yii\web\Application && $isAdminHost) {
            $app->getUrlManager()->addRules([
                '/' => $this->id . '/site/index',
                '<controller:\w+>/<action:[\w|-]+>' => $this->id . '/<controller>/<action>'
            ], false);
        } else {
            parent::bootstrap($app);
        }
        // 为视图层绑定行为
        Yii::$app->view->attachBehavior('viewBehavior', \admin\behaviors\ViewBehavior::className());
        // 改变错误页路由
        Yii::$app->errorHandler->errorAction = 'admin/site/error';
        // 定制表格样式
        Yii::$container->set('common\widgets\Table', [
            'deleteAllBtn' => true,
            'summaryOptions' => ['class' => 'summary cl pd-5 mt-10'],
            'tableOptions' => ['class' => 'table table-border table-bordered table-bg table-hover'],
            'beforeSearchRow' => function ($option, $index) {
                $option['type'] = isset($option['type']) ? $option['type'] : 'text';
                $option['options'] = isset($option['options']) ? $option['options'] : [];
                switch ($option['type']) {
                    case 'text':
                    case 'date':
                    case 'datetime':
                        $option['options']['class'] = isset($option['options']['class']) ? $option['options']['class'] : 'input-text';
                        break;
                    case 'dateRange':
                        $option['options']['style'] = 'width: 45%';
                        $option['options']['class'] = isset($option['options']['class']) ? $option['options']['class'] : 'input-text';
                        break;
                    case 'timeRange':
                        $option['options']['style'] = 'width: 45%';
                        $option['options']['class'] = isset($option['options']['class']) ? $option['options']['class'] : 'input-text';
                        break;
                    case 'select':
                        $option['options']['class'] = isset($option['options']['class']) ? $option['options']['class'] : 'select';
                        break;
                    default:
                        break;
                }
                return $option;
            }
        ]);
        // 定制表单样式
        Yii::$container->set('common\widgets\ActiveForm', [
            'submitRowOptions' => ['class' => 'row cl text-c']
        ]);
        Yii::$container->set('common\widgets\ActiveField', [
            'showLabel' => true,
            'options' => ['class' => 'row cl'],
            'template' => "<div class='formControls col-sm-9'>{input}</div>\n{hint}\n{error}",
            'labelOptions' => ['class' => 'form-label col-sm-2'],
            'inputOptions' => ['class' => 'input-text']
        ]);
    }
}
