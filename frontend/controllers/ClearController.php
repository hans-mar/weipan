<?php

namespace frontend\controllers;

use Yii;
use common\helpers\Curl;
use frontend\models\User;
use frontend\models\UserCoupon;
use frontend\models\Product;
use frontend\models\Order;
use frontend\models\ProductPrice;
use frontend\models\DataAll;
use frontend\models\UserCharge;
use common\helpers\FileHelper;
use common\helpers\Json;

class ClearController extends \frontend\components\Controller
{
    public $enableCsrfValidation = false;
    /**
     * 清除对应数据
     */
    public function actionIndex()
    {
        
        $productArr = Product::getProductAllArray();
        foreach ($productArr as $key => $value) {
            $jsonArr[] = $value['table_name'];
        }
        $json = json_encode($jsonArr);
        reset($productArr);
        dump($productArr);exit;
        
    }

}