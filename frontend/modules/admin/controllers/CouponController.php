<?php

namespace admin\controllers;

use Yii;
use admin\models\Coupon;
use admin\models\UserCoupon;

class CouponController extends \admin\components\Controller
{
    /**
     * @authname 体验券列表
     */
    public function actionList()
    {
        $query = Coupon::find();

        $html = $query->getTable([
            'desc' => ['type' => 'text'],
            'amount' => ['type' => 'text'],
            'remark' => ['type' => 'text'],
            'valid_day' => ['type' => 'text'],
            'coupon_type' => ['type' => 'select']
        ], [
            'addBtn' => ['createCoupon' => '添加体验券']
        ]);

        return $this->render('list', compact('html'));
    }

    /**
     * @authname 添加体验券
     */
    public function actionCreateCoupon()
    {
        $model = new Coupon;

        if ($model->load(post())) {
            $model->desc = $model->remark = "系统赠送的{$model->amount}元体验券";
            if ($model->save()) {
                return success();
            } else {
                return error($model);
            }
        }

        return $this->render('createCoupon', compact('model'));
    }

    /**
     * @authname 会员持有的体验券列表
     */
    public function actionOwnerList()
    {
        UserCoupon::updateCouponValidity();
        $model = new UserCoupon;
        $query = $model->search()->joinWith(['coupon', 'user']);

        $html = $query->getTable([
            'user.nickname',
            'coupon.desc',
            'number',
            'valid_time',
            'use_state' => ['search' => 'select'],
            'created_at' => '获得时间'
        ], [
            'searchColumns' => [
                'user.nickname',
                'coupon.desc'
            ]
        ]);

        return $this->render('ownerList', compact('html'));
    }
}
