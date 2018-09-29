<?php

namespace common\models;

use Yii;

/**
 * 这是表 `admin_account` 的模型
 */
class AdminAccount extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['admin_id', 'realname', 'id_card', 'bank_name', 'bank_card', 'bank_user', 'bank_mobile'], 'required'],
            [['admin_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['realname', 'id_card', 'bank_name', 'bank_card', 'bank_user', 'bank_address'], 'string', 'max' => 100],
            [['bank_mobile'], 'string', 'max' => 11]
        ];
    }

    public function attributeLabels()
    {
        return [
            'admin_id' => '用户ID',
            'realname' => '真实姓名',
            'id_card' => '身份证号',
            'bank_name' => '银行名称',
            'bank_card' => '银行卡号',
            'bank_user' => '持卡人姓名',
            'bank_mobile' => '银行预留手机号',
            'bank_address' => '开户行地址',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getRetail()
    {
        return $this->hasOne(Retail::className(), ['admin_id' => 'admin_id']);
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'adminAccount.admin_id' => $this->admin_id,
            ])
            ->andFilterWhere(['like', 'adminAccount.realname', $this->realname])
            ->andFilterWhere(['like', 'adminAccount.id_card', $this->id_card])
            ->andFilterWhere(['like', 'adminAccount.bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'adminAccount.bank_card', $this->bank_card])
            ->andFilterWhere(['like', 'adminAccount.bank_user', $this->bank_user])
            ->andFilterWhere(['like', 'adminAccount.bank_mobile', $this->bank_mobile])
            ->andFilterWhere(['like', 'adminAccount.bank_address', $this->bank_address])
            ->andFilterWhere(['like', 'adminAccount.created_at', $this->created_at])
            ->andFilterWhere(['like', 'adminAccount.updated_at', $this->updated_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
