<?php

namespace common\models;

use Yii;

/**
 * 这是表 `bank_card` 的模型
 */
class BankCard extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['user_id', 'id_card', 'bank_name', 'bank_card', 'bank_user', 'bank_mobile'], 'required'],
            [['user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['id_card', 'bank_name', 'bank_card', 'bank_user'], 'string', 'max' => 100],
            [['bank_mobile'], 'string', 'max' => 11],
            [['user_id'], 'unique']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'id_card' => '身份证号',
            'bank_name' => '银行名称',
            'bank_card' => '银行卡号',
            'bank_user' => '持卡人姓名',
            'bank_mobile' => '银行卡预留手机号',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    // public function getRelation()
    // {
    //     return $this->hasOne(Class::className(), ['foreign_key' => 'primary_key']);
    // }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'bankCard.id' => $this->id,
                'bankCard.user_id' => $this->user_id,
            ])
            ->andFilterWhere(['like', 'bankCard.id_card', $this->id_card])
            ->andFilterWhere(['like', 'bankCard.bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'bankCard.bank_card', $this->bank_card])
            ->andFilterWhere(['like', 'bankCard.bank_user', $this->bank_user])
            ->andFilterWhere(['like', 'bankCard.bank_mobile', $this->bank_mobile])
            ->andFilterWhere(['like', 'bankCard.created_at', $this->created_at])
            ->andFilterWhere(['like', 'bankCard.updated_at', $this->updated_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
