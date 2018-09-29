<?php

namespace admin\components;

use Yii;

class Controller extends \common\components\WebController
{
    public $layout = 'frame';

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        return $result;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['error', 'delete', 'upload', 'captcha', 'ajax-update', 'delete-all']
                    ],
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if ($action->controller->id === 'site') {
                                return true;
                            }
                            $actionName = $action->controller->id . '/' . lcfirst(str_replace('action', '', $action->actionMethod));
                            return u()->can($actionName);
                        }
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (req()->isAjax) {
                        return self::error('您没有操作权限~!');
                    } else {
                        self::throwHttpException('您没有操作权限~!');
                    }
                }
            ],
        ];
    }
}
