<?php

namespace common\models;

use Yii;

/**
 * 这是表 `data_all` 的模型
 */
class DataAll extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['time'], 'safe'],
            [['diff'], 'number'],
            [['name'], 'string', 'max' => 20],
            [['name'], 'unique']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '产品名称',
            'price' => '当前价格',
            'time' => '当前时间',
            'diff' => '涨跌值',
            'diff_rate' => '涨跌%',
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
                'dataAll.diff' => $this->diff,
                'dataAll.diff_rate' => $this->diff_rate,
            ])
            ->andFilterWhere(['like', 'dataAll.name', $this->name])
            ->andFilterWhere(['like', 'dataAll.price', $this->price])
            ->andFilterWhere(['like', 'dataAll.time', $this->time])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    /**
     * 获取最新一条期货价格数组
     * @access public
     * @return array
     */
    public static function newProductPrice($name)
    {
        //判断不是不是数字
        if (is_numeric($name)) {
            $product = Product::findOne($name);
            if (!empty($product)) {
                $name = $product->table_name;
            }
        }
        return self::findModel($name);
    }
    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
