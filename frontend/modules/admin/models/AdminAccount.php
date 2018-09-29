<?php

namespace admin\models;

use Yii;

class AdminAccount extends \common\models\AdminAccount
{
    public function rules()
    {
        return array_merge(parent::rules(), [

        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            // 'field1' => 'description1',
            // 'field2' => 'description2',
        ]);
    }
}
