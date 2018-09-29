<?php

namespace common\models;

use Yii;

/**
 * 这是表 `user_coupon` 的模型
 */
class UserCoupon extends \common\components\ARModel
{
    const USE_STATE_WAIT = 1;
    const USE_STATE_USE = 2;
    const USE_STATE_PASS = -1;

    public function rules()
    {
        return [
            [['coupon_id', 'user_id'], 'required'],
            [['coupon_id', 'user_id', 'use_state', 'number'], 'integer'],
            [['valid_time', 'created_at', 'updated_at'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'coupon_id' => 'Coupon ID',
            'user_id' => 'User ID',
            'use_state' => '使用状态：1未使用，2已使用，-1已过期',
            'number' => '数量',
            'valid_time' => '过期时间',
            'created_at' => '获得时间',
            'updated_at' => '使用时间',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getCoupon()
    {
        return $this->hasOne(Coupon::className(), ['id' => 'coupon_id']);
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
                'userCoupon.id' => $this->id,
                'userCoupon.coupon_id' => $this->coupon_id,
                'userCoupon.user_id' => $this->user_id,
                'userCoupon.use_state' => $this->use_state,
                'userCoupon.number' => $this->number,
            ])
            ->andFilterWhere(['like', 'userCoupon.valid_time', $this->valid_time])
            ->andFilterWhere(['like', 'userCoupon.created_at', $this->created_at])
            ->andFilterWhere(['like', 'userCoupon.updated_at', $this->updated_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    public static function updateCouponValidity()
    {
        self::find()
            ->with('coupon')
            ->where('DATE_ADD(userCoupon.created_at, INTERVAL coupon.valid_day DAY) <= NOW()')
            ->update(['use_state' => self::USE_STATE_PASS]);
    }

    public static function sendCoupon($uids, $couponId, $number = 1)
    {
        $uids = (array) $uids;
        $coupon = Coupon::findOne($couponId);
        foreach ($uids as $uid) {
            self::dbInsert('user_coupon', ['coupon_id' => $couponId, 'number' => $number, 'user_id' => $uid, 'valid_time' => date('Y-m-d H:i:s', time() + $coupon->valid_day * 3600 * 24), 'created_at' => self::$time]);
        }
    }

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/

    // Map method of field `use_state`
    public static function getUseStateMap($prepend = false)
    {
        $map = [
            self::USE_STATE_WAIT => '未使用',
            self::USE_STATE_USE => '已使用',
            self::USE_STATE_PASS => '已过期',
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `use_state`
    public function getUseStateValue($value = null)
    {
        return $this->resetValue($value);
    }
}
