<?php

namespace admin\models;

use Yii;

class UserCoupon extends \common\models\UserCoupon
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
            'use_state' => '使用状态',
            // 'field2' => 'description2',
        ]);
    }
}
