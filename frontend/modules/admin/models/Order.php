<?php

namespace admin\models;

use Yii;

class Order extends \common\models\Order
{
    public $is_profit;
    public $start_time;
    public $end_time;
    
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
            'order_state' => '持仓状态',
            // 'field2' => 'description2',
        ]);
    }

    public function listQuery()
    {
        $query = $this->search()
            ->andFilterWhere(['>=', 'order.created_at', $this->start_time])
            ->andFilterWhere(['<=', 'order.created_at', $this->end_time])
            ->andWhere(['sim'=>1])
            ->andFilterWhere(['order.product_id' => $this->product_name]);
        if ($this->is_profit) {
            $query->andFilterWhere([$this->is_profit, 'profit', 0]);
        }
        return $query;
    }

    public static function getIsProfitMap($prepend = false)
    {
        $map = [
            '>' => '盈利',
            '<' => '亏损'
        ];

        return self::resetMap($map, $prepend);
    }

    public function getIsProfitValue($value = null)
    {
        return $this->resetValue($value);
    }
}
