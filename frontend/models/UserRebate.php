<?php

namespace frontend\models;

use Yii;

class UserRebate extends \common\models\UserRebate
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_date', 'end_date'], 'safe'],
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

    //经纪人时间搜索
    public function managerTimeQuery()
    {
        $this->load(get());
        $this->start_date = $this->start_date ?: date('Y-m-d', strtotime('-31 days'));
        $this->end_date = $this->end_date ?: date('Y-m-d', strtotime('+1 days'));
        // test($this->attributes, $this->start_date, $this->end_date,$_GET);
        return $this->search()
                    ->with(['user', 'order'])
                    ->andWhere(['pid' => u()->id])
                    ->andFilterWhere(['between', 'created_at', $this->start_date, $this->end_date])
                    ->orderBy('created_at DESC');
    }
}
