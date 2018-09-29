<?php

namespace common\models;

use Yii;

/**
 * 这是表 `admin_withdraw` 的模型
 */
class AdminWithdraw extends \common\components\ARModel
{
    const OP_STATE_WAIT = 1;
    const OP_STATE_PASS = 2;
    const OP_STATE_DENY = -1;

    public function rules()
    {
        return [
            [['admin_id', 'amount'], 'required'],
            [['admin_id', 'op_state', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin_id' => '用户ID',
            'amount' => '出金金额',
            'op_state' => '操作状态',
            'created_at' => '申请时间',
            'created_by' => 'Created By',
            'updated_at' => '审核时间',
            'updated_by' => 'Updated By',
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
                'adminWithdraw.id' => $this->id,
                'adminWithdraw.admin_id' => $this->admin_id,
                'adminWithdraw.amount' => $this->amount,
                'adminWithdraw.op_state' => $this->op_state,
                'adminWithdraw.created_by' => $this->created_by,
                'adminWithdraw.updated_by' => $this->updated_by,
            ])
            ->andFilterWhere(['like', 'adminWithdraw.created_at', $this->created_at])
            ->andFilterWhere(['like', 'adminWithdraw.updated_at', $this->updated_at])
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
