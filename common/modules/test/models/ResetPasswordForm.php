<?php
namespace common\modules\test\models;

use Yii;

class ResetPasswordForm extends \common\components\Model
{
    public $name;
    public $password;

    public function rules()
    {
        return [
            ['password', 'required'],
            ['name', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['state' => 1],
                'message' => 'There is no user with such name.'
            ],
        ];
    }
}