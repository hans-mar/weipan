<?php

namespace api\components;

use Yii;

/**
 * RESTful 控制器的基类
 *
 * yii\rest\ActiveController 默认提供以下操作:
 *
 * yii\rest\IndexAction: 按页列出资源;
 * yii\rest\ViewAction: 返回指定资源的详情;
 * yii\rest\CreateAction: 创建新的资源;
 * yii\rest\UpdateAction: 更新一个存在的资源;
 * yii\rest\DeleteAction: 删除指定的资源;
 * yii\rest\OptionsAction: 返回支持的HTTP方法.
 *
 * @author ChisWill
 */
class ActiveController extends \yii\rest\ActiveController
{
    use common\traits\ChisWill;
    
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            
            $this->initUser();

            return true;
        }
        
        return false;
    }
}
