<?php

namespace admin\models;

use Yii;

class UserWithdraw extends \common\models\UserWithdraw
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
        return $this->search()
            ->manager()
            ->andWhere(['user.state' => self::STATE_VALID])
            ->andFilterWhere(['>=', 'userWithdraw.updated_at', $this->start_time])
            ->andFilterWhere(['<=', 'userWithdraw.updated_at', $this->end_time]);
    }
}
