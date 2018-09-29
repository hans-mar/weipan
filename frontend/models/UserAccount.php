<?php

namespace frontend\models;

use Yii;

class UserAccount extends \common\models\UserAccount
{
    public $verifyCode;

    public function rules()
    {
        return array_merge(parent::rules(), [
            // 短信验证码
            [['verifyCode'], 'verifyCode', 'on' => 'withDraw'],
            [['verifyCode'], 'required', 'on' => 'withDraw'],
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'withDraw' => ['verifyCode', 'user_id', 'realname', 'id_card', 'bank_name', 'bank_card', 'bank_user', 'bank_mobile', 'bank_address', 'address'],
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
