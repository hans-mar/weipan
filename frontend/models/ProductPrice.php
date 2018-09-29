<?php

namespace frontend\models;

use Yii;

class ProductPrice extends \common\models\ProductPrice
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            // [['field1', 'field2'], 'required', 'message' => '{attribute} is required'],
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            // 'scenario' => ['field1', 'field2'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            // 'field1' => 'description1',
            // 'field2' => 'description2',
        ]);
    }

    public static function getSetProductPrice($product_id)
    {
        $data = self::find()->where(['product_id' => $product_id])->orderBy('deposit ASC')->all();
        $arr = [];
        foreach ($data as $product) {
            $arr[] = $product->attributes;
        }
        return $arr;
    }
}
