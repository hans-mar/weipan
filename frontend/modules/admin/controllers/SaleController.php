<?php

namespace admin\controllers;

use Yii;
use admin\models\User;
use admin\models\AdminUser;
use admin\models\UserExtend;
use admin\models\Retail;
use admin\models\UserRebate;
use common\helpers\Hui;
use common\helpers\Html;

class SaleController extends \admin\components\Controller
{
    /**
     * @authname 经纪人列表
     */
    public function actionManagerList()
    {
        $query = (new User)->managerQuery()->joinWith(['userAccount', 'userExtend', 'admin'])->orderBy('total_fee DESC')->manager();

        $html = $query->getTable([
            'id' => ['search' => true],
            'userExtend.realname' => ['search' => true, 'header' => '真实姓名'],
            'nickname' => ['search' => true],
            'userExtend.mobile' => ['search' => true, 'header' => '经纪人手机号'],
            // 'mobile' => ['search' => true, 'header' => '注册手机号'],
            // 'pid' => ['header' => '推荐人', 'value' => function ($row) {
            //     return $row->getParentLink();
            // }],
            'admin.username' => ['header' => '代理商账户'],
            'admin.pid' => ['header' => '综会账号', 'value' => function ($row) {
                return $row->getLeaderName($row->admin_id);
            }],
            'total_fee',
            'userExtend.point' => ['header' => '返点(%)'],
            'account',
            'created_at',
            ['type' => ['delete'], 'width' => '250px', 'value' => function ($row) {
                return Hui::primaryBtn('修改返点', ['editPoint', 'id' => $row->id], ['class' => 'editBtn']);
            }]
        ], [
            'searchColumns' => [
                'admin.username' => ['header' => '代理商账户'],
                'leader' => ['header' => '综会账号'],
            ]
        ]);

        return $this->render('managerList', compact('html'));
    }

    /**
     * @authname 修改经纪人返点%
     */
    public function actionEditPoint() 
    {
        $userExtend = UserExtend::findModel(get('id'));
        $retail = Retail::find()->where(['account' => u()->username])->one();
        if (empty($retail)) {
            $retail = Retail::find()->joinWith(['adminUser'])->where(['adminUser.id' => $userExtend->coding])->one();
        }
        $userExtend->point = post('point');
        if ($userExtend->point > $retail->point || is_int($userExtend->point) || $userExtend->point < 0) {
            return error('经纪人的返点不能大于上级代理商的返点'.$retail->point.'(设置返点为正整数)');
        }
        if ($userExtend->validate()) {
            $userExtend->update(false);
            return success();
        } else {
            return error($user);
        }
    }

    /**
     * @authname 代理商返点统计
     */
    public function actionManagerRebateList()
    {
        $query = (new UserRebate)->managerListQuery()->orderBy('userRebate.created_at DESC')->manager();
        $count = $query->sum('amount') ?: 0;

        $html = $query->getTable([
            'id',
            'admin.username' => ['header' => '管理员账号'],
            'user.nickname' => ['header' => '会员昵称（手机号）', 'value' => function ($row) {
                return Html::a($row->user->nickname . "({$row->user->mobile})", ['', 'search[user.id]' => $row->user->id], ['class' => 'parentLink']);
            }],
            'amount',
            'point' => function ($row) {
                return $row->point . '%';
            },
            'created_at' => '返点时间'
        ], [
            'searchColumns' => [
                'admin.username' => ['header' => '管理员账号'],
                'user.id' => ['header' => '会员ID'],
                'user.mobile' => ['header' => '会员手机'],
                'time' => 'timeRange'
            ],
            'ajaxReturn' => [
                'count' => $count
            ]
        ]);

        return $this->render('managerRebateList', compact('html', 'count'));
    }
}
