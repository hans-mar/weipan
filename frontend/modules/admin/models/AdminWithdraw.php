<?php

namespace admin\models;

use Yii;

class AdminWithdraw extends \common\models\AdminWithdraw
{
    public $start_time;
    public $end_time;

    public function rules()
    {
        return array_merge(parent::rules(), [
            // 用户提现
            [['amount'], 'required', 'on' => ['withdraw']],
            // 提现验证金额
            [['amount'], 'validateWithdrawAmount'],
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'withdraw' => ['amount'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            // 'field1' => 'description1',
            // 'field2' => 'description2',
        ]);
    }

    public function validateWithdrawAmount()
    {
        if (!is_numeric($this->amount)) {
            return $this->addError('amount', '取现金额必须是数字！');
        }
        if ($this->amount < 100) {
            return $this->addError('amount', '取现不能小于100元！');
        }
        if ($this->amount > 20000) {
            return $this->addError('amount', '提现金额不能超过20000元！');
        }
    }
    
    public function listQuery()
    {
        $query = $this->search()
            ->joinWith(['retail.adminUser'])
            ->andFilterWhere(['>=', 'adminWithdraw.created_at', $this->start_time])
            ->andFilterWhere(['<=', 'adminWithdraw.created_at', $this->end_time]);
        if (u()->power < AdminUser::POWER_ADMIN) {
            $query->andWhere(['adminWithdraw.admin_id' => u()->id]); 
        }
        return $query;
    }
}
