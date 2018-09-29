<?php

namespace admin\models;

use Yii;

class Retail extends \common\models\Retail
{
    public $file1;
    public $file2;
    public $file3;
    public $file4;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['file1', 'file2', 'file3', 'file4'], 'file', 'extensions' => 'jpg, png', 'skipOnEmpty' => true, 'maxSize' => 1024 * 2 * 1000],
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
            'file1' => '法人身份证',
            'file2' => '营业执照',
            'file3' => '组织机构代码证',
            'file4' => '税务登记证',
        ]);
    }
}
