<?php

namespace common\models;

use Yii;

/**
 * 这是表 `user_rebate` 的模型
 */
class UserRebate extends \common\components\ARModel
{
    public $start_date;
    public $end_date;
    
    public function rules()
    {
        return [
            [['order_id', 'user_id', 'pid', 'point'], 'integer'],
            [['user_id', 'pid', 'amount', 'point'], 'required'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单id',
            'user_id' => '返点用户ID',
            'pid' => '经纪人用户id',
            'amount' => '返点金额',
            'point' => '返点百分比%',
            'created_at' => '申请时间',
            'updated_at' => '审核时间',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getParent()
    {
        return $this->hasOne(User::className(), ['id' => 'pid']);
    }

    public function getAdmin()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'pid']);
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'userRebate.id' => $this->id,
                'userRebate.order_id' => $this->order_id,
                'userRebate.user_id' => $this->user_id,
                'userRebate.pid' => $this->pid,
                'userRebate.amount' => $this->amount,
                'userRebate.point' => $this->point,
            ])
            ->andFilterWhere(['like', 'userRebate.created_at', $this->created_at])
            ->andFilterWhere(['like', 'userRebate.updated_at', $this->updated_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    //是否给用户返点
    public static function isUserRebate($order)
    {
        //var_dump($order);
        //exit;
        $user = User::findOne($order->user_id);
        $point = 0;
        //用户是否有经纪人
        if ($user->pid > 0) {
            $pUser = User::find()->with(['userExtend'])->where(['id' => $user->pid, 'is_manager' => User::IS_MANAGER_YES])->one();
            if (!empty($pUser)) {
                $point = $pUser->userExtend->point;
                //经纪人账户表
                $sale = $order->fee * $pUser->userExtend->point / 100;
                $sale = floor($sale * 100) / 100;
                if ($sale > 0) {
                    $pUser->userExtend->rebate_account += $sale;
                    $pUser->userExtend->update();

                    $pUser->total_fee += $sale;
                    $pUser->account += $sale;
                    $pUser->update();
                    //返点记录表
                    $userRebate = new UserRebate();
                    $userRebate->user_id = $user->id;
                    $userRebate->order_id = $order->id;
                    $userRebate->pid = $pUser->id;
                    $userRebate->amount = $sale;
                    $userRebate->point = $pUser->userExtend->point;
                    $userRebate->insert();
                }
            }
        }
        //代理商->综会->超管
        if ($user->admin_id > 0) {
            $adminUser = AdminUser::find()->with(['retail'])->where(['id' => $user->admin_id])->one();
            if (!empty($adminUser)) {
                $admin_id = $adminUser->pid;
                $sale = $order->fee * ($adminUser->retail->point - $point) / 100;
                $sale = floor($sale * 100) / 100;

                if ($sale > 0) {
                    $adminUser->retail->total_fee += $sale;
                    $adminUser->retail->update();

                    //返点记录表
                    $userRebate = new UserRebate();
                    $userRebate->user_id = $user->id;
                    $userRebate->order_id = $order->id;
                    $userRebate->pid = $adminUser->id;
                    $userRebate->amount = $sale;
                    $userRebate->point = $adminUser->retail->point - $point;
                    $userRebate->insert();
                    $point = $adminUser->retail->point;
                    //综会
                    $adminUser = AdminUser::find()->with(['leader'])->where(['id' => $admin_id])->one();
                    $admin_id = $adminUser->pid;
                    $sale = $order->fee * ($adminUser->leader->point - $point) / 100;
                    $sale = floor($sale * 100) / 100;
                    if ($sale > 0) {
                        $adminUser->leader->deposit += $sale;
                        $adminUser->leader->update();

                        //返点记录表
                        $userRebate = new UserRebate();
                        $userRebate->user_id = $user->id;
                        $userRebate->order_id = $order->id;
                        $userRebate->pid = $adminUser->id;
                        $userRebate->amount = $sale;
                        $userRebate->point = $adminUser->leader->point - $point;
                        $userRebate->insert();
                        $point = $adminUser->leader->point;
                        //超管
                        $adminUser = AdminUser::find()->with(['leader'])->where(['id' => $admin_id])->one();
                        $admin_id = $adminUser->pid;
                        $sale = $order->fee * ($adminUser->leader->point - $point) / 100;
                        $sale = floor($sale * 100) / 100;
                        if ($sale > 0) {
                            $adminUser->leader->deposit += $sale;
                            $adminUser->leader->update();

                            //返点记录表
                            $userRebate = new UserRebate();
                            $userRebate->user_id = $user->id;
                            $userRebate->order_id = $order->id;
                            $userRebate->pid = $adminUser->id;
                            $userRebate->amount = $sale;
                            $userRebate->point = $adminUser->leader->point - $point;
                            $userRebate->insert();
                        }
                    }
                }
            }
        }
    }
    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
