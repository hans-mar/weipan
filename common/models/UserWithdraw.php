<?php

namespace common\models;

use Yii;

/**
 * 这是表 `user_withdraw` 的模型
 */
class UserWithdraw extends \common\components\ARModel
{
    const OP_STATE_WAIT = 1;
    const OP_STATE_PASS = 2;
    const OP_STATE_DENY = -1;

    public function rules()
    {
        return [
            [['user_id', 'amount', 'account_id'], 'required'],
            [['user_id', 'account_id', 'op_state'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'amount' => '出金金额',
            'account_id' => '出金账号ID',
            'op_state' => '操作状态：1待审核，2已操作，-1不通过',
            'created_at' => '申请时间',
            'updated_at' => '审核时间',
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
                'userWithdraw.id' => $this->id,
                'userWithdraw.user_id' => $this->user_id,
                'userWithdraw.amount' => $this->amount,
                'userWithdraw.account_id' => $this->account_id,
                'userWithdraw.op_state' => $this->op_state,
            ])
            ->andFilterWhere(['like', 'userWithdraw.created_at', $this->created_at])
            ->andFilterWhere(['like', 'userWithdraw.updated_at', $this->updated_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/
    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/

    // Map method of field `op_state`
    public static function getOpStateMap($prepend = false)
    {
        $map = [
            self::OP_STATE_WAIT => '待审核',
            self::OP_STATE_PASS => '已通过',
            self::OP_STATE_DENY => '不通过',
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `op_state`
    public function getOpStateValue($value = null)
    {
        return $this->resetValue($value);
    }
}
