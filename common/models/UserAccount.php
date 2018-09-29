<?php

namespace common\models;

use Yii;

/**
 * 这是表 `user_account` 的模型
 */
class UserAccount extends \common\components\ARModel
{
    const BANK_NAME_ZS = 1;
    const BANK_NAME_GS = 2;
    const BANK_NAME_JS = 3;
    const BANK_NAME_NY = 4;
    const BANK_NAME_MS = 5;
    const BANK_NAME_XY = 6;
    const BANK_NAME_JT = 7;
    const BANK_NAME_GD = 8;

    public function rules()
    {
        return [
            [['user_id', 'realname', 'id_card', 'bank_name', 'bank_card', 'bank_user', 'bank_mobile'], 'required'],
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['realname', 'id_card', 'bank_name', 'bank_card', 'bank_user', 'bank_address'], 'string', 'max' => 100],
            [['bank_mobile'], 'string', 'max' => 11],
            [['address'], 'string', 'max' => 150],
            [['user_id'], 'unique']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'realname' => '真实姓名',
            'id_card' => '身份证号',
            'bank_name' => '银行名称',
            'bank_card' => '银行卡号',
            'bank_user' => '持卡人姓名',
            'bank_mobile' => '银行预留手机号',
            'bank_address' => '开户行地址',
            'address' => '地址',
            'created_at' => 'Created At',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'userAccount.id' => $this->id,
                'userAccount.user_id' => $this->user_id,
            ])
            ->andFilterWhere(['like', 'userAccount.realname', $this->realname])
            ->andFilterWhere(['like', 'userAccount.id_card', $this->id_card])
            ->andFilterWhere(['like', 'userAccount.bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'userAccount.bank_card', $this->bank_card])
            ->andFilterWhere(['like', 'userAccount.bank_user', $this->bank_user])
            ->andFilterWhere(['like', 'userAccount.bank_mobile', $this->bank_mobile])
            ->andFilterWhere(['like', 'userAccount.bank_address', $this->bank_address])
            ->andFilterWhere(['like', 'userAccount.address', $this->address])
            ->andFilterWhere(['like', 'userAccount.created_at', $this->created_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/

    // Map method of field `op_style`
    public static function getBankNameMap($prepend = false)
    {
        $map = [
            self::BANK_NAME_ZS => '招商银行',
            self::BANK_NAME_GS => '中国工商银行',
            self::BANK_NAME_JS => '中国建设银行',
            self::BANK_NAME_NY => '中国农业银行',
            self::BANK_NAME_MS => '中国民生银行',
            self::BANK_NAME_XY => '兴业银行总行',
            self::BANK_NAME_JT => '交通银行',
            self::BANK_NAME_GD => '中国光大银行',
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `op_style`
    public function getBankNameValue($value = null)
    {
        return $this->resetValue($value);
    }
}
