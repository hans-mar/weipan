<?php

namespace admin\models;

use Yii;

class UserCharge extends \common\models\UserCharge
{
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
            'charge_type' => '支付方式',
            // 'field2' => 'description2',
        ]);
    }

    public function listQuery()
    {
        return $this->search()
            ->andWhere(['charge_state' => UserCharge::CHARGE_STATE_PASS])
            ->andFilterWhere(['>=', 'userCharge.created_at', $this->start_time])
            ->andFilterWhere(['<=', 'userCharge.created_at', $this->end_time]);
    }
}
