<?php

namespace common\models;

use Yii;

/**
 * 这是表 `product_param` 的模型
 */
class ProductParam extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['product_id', 'start_price', 'end_price', 'start_point', 'end_point'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'product_id' => 'Product ID',
            'start_price' => '起始价格',
            'end_price' => '截止价格',
            'start_point' => '起始点数',
            'end_point' => '截止点数',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    // public function getRelation()
    // {
    //     return $this->hasOne(Class::className(), ['foreign_key' => 'primary_key']);
    // }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'productParam.product_id' => $this->product_id,
                'productParam.start_price' => $this->start_price,
                'productParam.end_price' => $this->end_price,
                'productParam.start_point' => $this->start_point,
                'productParam.end_point' => $this->end_point,
            ])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
