<?php

namespace admin\models;

use Yii;

class ExuserWithdraw extends \common\models\ExuserWithdraw
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
            'op_state' => '申请状态',
            // 'field2' => 'description2',
        ]);
    }

    public function listQuery()
    {
        $query = $this->search()
        ->joinWith(['userExtend'])
        ->andFilterWhere(['>=', 'exuserWithdraw.created_at', $this->start_time])
        ->andFilterWhere(['<=', 'exuserWithdraw.created_at', $this->end_time]);
        if (u()->power < AdminUser::POWER_ADMIN) {
        $query->andWhere(['exuserWithdraw.coding' => u()->id]); 
    }
    return $query;
    }
}
