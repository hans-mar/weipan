<?php

namespace admin\models;

use Yii;

class UserRebate extends \common\models\UserRebate
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
            // 'field1' => 'description1',
            // 'field2' => 'description2',
        ]);
    }

    public function listQuery()
    {
        return $this->search()
                    ->joinWith(['user', 'parent', 'admin'])
                    ->andWhere('userRebate.pid > ' . AdminUser::POWER_SUPER)
                    ->andFilterWhere(['>=', 'userRebate.created_at', $this->start_time])
                    ->andFilterWhere(['<=', 'userRebate.created_at', $this->end_time]);
    }

    public function managerListQuery()
    {
        return $this->search()
                    ->joinWith(['user', 'admin'])
                    ->andWhere('userRebate.pid < ' . AdminUser::POWER_SUPER)
                    ->andFilterWhere(['>=', 'userRebate.created_at', $this->start_time])
                    ->andFilterWhere(['<=', 'userRebate.created_at', $this->end_time]);
    }
}
