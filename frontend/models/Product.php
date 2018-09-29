<?php

namespace frontend\models;

use Yii;

class Product extends \common\models\Product
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
    //获取首页三个上架的产品
    public static function getIndexProduct()
    {
        $products = self::find()->where(['on_sale' => self::ON_SALE_YES, 'state' => self::STATE_VALID])->limit(3)->orderBy('hot DESC')->all();
        $arr = [];
        foreach ($products as $product) {
            $arr[$product->id]['id'] = $product->id; 
            $arr[$product->id]['name'] = $product->name; 
            $arr[$product->id]['table_name'] = $product->table_name; 
            $arr[$product->id]['source'] = $product->source; 
            $newData = DataAll::newProductPrice($product->id);
            $arr[$product->id]['price'] = $newData->price; 
            $arr[$product->id]['close'] = $newData->close; 
            $arr[$product->id]['diff_rate']=$newData->diff_rate;
        }
        return $arr;
    }

    //获取上架的产品数组
    public static function getProductArray()
    {
        return self::find()->where(['on_sale' => self::ON_SALE_YES, 'state' => self::STATE_VALID])->orderBy('hot DESC')->map('id', 'name');
    }
      public static function getProductId($table_name)//根据表名获得产品id
    {
        return self::find()->select('id')->where(['table_name'=>$table_name])->one();
    }
    //获取所有上架的产品数组
    public static function getProductAllArray()
    {
        $products=self::find()->where(['on_sale' => self::ON_SALE_YES, 'state' => self::STATE_VALID])->orderBy('hot DESC')->all();
        $arr = [];
        foreach ($products as $product) {
           $arr[$product->id]['id'] = $product->id; 
            $arr[$product->id]['name'] = $product->name; 
            $arr[$product->id]['table_name'] = $product->table_name; 
            $arr[$product->id]['source'] = $product->source; 
            $newData = DataAll::newProductPrice($product->id);
            $arr[$product->id]['price'] = $newData->price; 
            $arr[$product->id]['close'] = $newData->close; 
            $arr[$product->id]['diff_rate']=$newData->diff_rate;
        }
        return $arr;
    }
}
