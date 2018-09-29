<?php

namespace frontend\models;

use Yii;

class BankCard extends \common\models\BankCard
{
    public $verifyCode;
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['bank_name', 'id_card', 'bank_card', 'bank_user', 'bank_mobile'], 'required', 'on' => 'bank'],
            [['id_card'], 'string', 'min' => 18],
            [['bank_card'], 'string', 'min' => 12],
            [['bank_mobile'], 'string', 'min' => 11],
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'bank' => ['bank_name', 'id_card', 'bank_card', 'bank_user', 'bank_mobile'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'verifyCode' => '短信验证码',
        ]);
    }
    public function verifyCode()
    {
        
        if ($this->verifyCode != session('verifyCode')) {
            $this->addError('verifyCode', '短信验证码不正确');
        }
    }
}
