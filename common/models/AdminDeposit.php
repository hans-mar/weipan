<?php

namespace common\models;

use Yii;

/**
 * 这是表 `admin_deposit` 的模型
 */
class AdminDeposit extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['order_id', 'admin_id'], 'required'],
            [['order_id', 'user_id', 'admin_id'], 'integer'],
            [['amount'], 'number'],
            [['created_at'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单id',
            'user_id' => '头寸用户',
            'admin_id' => '账号',
            'amount' => '金额',
            'created_at' => '创建时间',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getAdminUser()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'admin_id']);
    }

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
                'adminDeposit.id' => $this->id,
                'adminDeposit.order_id' => $this->order_id,
                'adminDeposit.user_id' => $this->user_id,
                'adminDeposit.admin_id' => $this->admin_id,
                'adminDeposit.amount' => $this->amount,
            ])
            ->andFilterWhere(['like', 'adminDeposit.created_at', $this->created_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    public static function depositRecord($order)
    {
        $user = User::findOne($order->user_id);
        $adminDeposit = new AdminDeposit();
        if ($user->admin_id > 0 && $order->profit != 0) {
            $adminUser = AdminUser::find()->with('parent')->where(['id' => $user->admin_id])->one();
            $adminLeader = AdminLeader::findOne($adminUser->parent->id);
            if (!empty($adminLeader)) {
                $adminLeader->deposit -= $order->profit;
                $adminLeader->update();

                $adminDeposit->admin_id = $adminLeader->admin_id;
                $adminDeposit->order_id = $order->id;
                $adminDeposit->amount = $order->profit;
                $adminDeposit->user_id = $user->id;
                $adminDeposit->save();
            }
        }
    }
    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
