<?php

namespace admin\models;

use Yii;

class Log extends \common\models\Log
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
            'id' => '序号',
            'level' => '错误级别',
            'category' => '错误分类',
            'log_time' => '记录时间',
            'prefix' => '额外信息',
            'message' => '错误内容'
        ]);
    }

    public function logListQuery()
    {
        $query = $this->search();
        if ($this->start_time) {
            $query->andWhere(['>=', 'log_time', strtotime($this->start_time)]);
        }
        if ($this->end_time) {
            $query->andWhere(['<=', 'log_time' , strtotime($this->end_time) + 86400]);
        }
        return $query->orderBy('id DESC');
    }
}
