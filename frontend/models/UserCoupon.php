<?php

namespace frontend\models;

use Yii;

class UserCoupon extends \common\models\UserCoupon
{
    public $couponId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            // [['field1', 'field2'], 'required', 'message' => '{attribute} is required'],
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            // 'scenario' => ['field1', 'field2'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            // 'field1' => 'description1',
            // 'field2' => 'description2',
        ]);
    }

    public static function getNumberType($pid)
    {
        $coupon = self::find()->andWhere(['use_state' => self::USE_STATE_WAIT, 'user_id' => u()->id])->andWhere(['>', 'number', 0])->andWhere(['>', 'valid_time', self::$time])->andWhere(['coupon_type' => $pid])->joinWith(['coupon'])->groupBy('coupon_id')->orderBy('amount ASC')->all();
        $arr = [];
        foreach ($coupon as $model) {
            $userCoupon = self::getCouponIdCount($model->coupon_id);
            // $userCoupon = self::find()->andWhere(['use_state' => self::USE_STATE_WAIT, 'user_id' => u()->id, 'coupon_id' => $model->coupon_id])->andWhere(['>', 'number', 0])->andWhere(['>', 'valid_time', self::$time])->select('SUM(number) AS number')->one();
            $arr[floatval($model->coupon->amount)] = $userCoupon->number;
        }
        return $arr;
    }

    //这个用户该期货的体验卷总数
    public static function getCouponIdCount($coupon_id, $product_id = 0)
    {
        if ($product_id > 0) {
            $coupon_id = Coupon::find()->where(['amount' => $coupon_id, 'coupon_type' => $product_id])->one()->id;
        }
        return self::find()->andWhere(['use_state' => self::USE_STATE_WAIT, 'user_id' => u()->id, 'coupon_id' => $coupon_id])->andWhere(['>', 'number', 0])->andWhere(['>', 'valid_time', self::$time])->select('SUM(number) AS number')->one();
    }

    //获得这个用户体验卷总数
    public static function getAllUserCouponCount()
    {
        if (user()->isGuest) {
            return 0;
        }
        if (session('usercoupon_' . u()->id)) {
            return session('usercoupon_' . u()->id);
        }
        $coupon = self::find()->andWhere(['use_state' => self::USE_STATE_WAIT, 'user_id' => u()->id])->andWhere(['>', 'number', 0])->andWhere(['>', 'valid_time', self::$time])->select('SUM(number) AS number')->one();
        session('usercoupon_' . u()->id, $coupon->number, 3600);
        return $coupon->number;
    }

    //扣除用户使用该期货的体验卷
    public static function deleteUserCoupon($coupon_id, $hand, $product_id = 0)
    {
        if ($product_id > 0) {
            $coupon_id = Coupon::find()->where(['amount' => $coupon_id, 'coupon_type' => $product_id])->one()->id;
        }

        $userCoupon = self::find()->andWhere(['use_state' => self::USE_STATE_WAIT, 'user_id' => u()->id, 'coupon_id' => $coupon_id])->andWhere(['>', 'number', 0])->andWhere(['>', 'valid_time', self::$time])->orderBy('valid_time ASC')->all();
        //删除该用户的体验卷
        foreach ($userCoupon as $model) {
            if ($hand < 1) {
                break;
            }
            if ($hand >= $model->number) {
                $model->use_state = self::USE_STATE_USE;
                $model->update();
                $hand -= $model->number;
            } else {
                $model->number -= $hand;
                $model->update();
                break;
            }
        }
    }
}
