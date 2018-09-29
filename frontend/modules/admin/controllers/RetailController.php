<?php

namespace admin\controllers;

use Yii;
use admin\models\Retail;
use admin\models\AdminUser;
use admin\models\AdminAccount;
use admin\models\AdminWithdraw;
use admin\models\ExuserWithdraw;
use admin\models\AdminLeader;
use admin\models\User;
use common\helpers\Hui;
use common\helpers\Html;
use common\helpers\StringHelper;

class RetailController extends \admin\components\Controller
{
    /**
     * @authname 代理商列表
     */
    public function actionList()
    {
        $query = (new Retail)->search()->joinWith('adminUser')->retail();

        $html = $query->getTable([
            'admin_id',
            'account' => ['search' => true],
            'company_name' => ['type' => 'text', 'search' => true],
            'realname' => ['type' => 'text', 'search' => true],
            'tel' => ['type' => 'text', 'search' => true],
            // 'deposit' => ['type' => 'text', 'search' => true],
            'point',
            'total_fee',
            'code',
            'created_at',
            ['type' => ['delete'], 'width' => '250px', 'value' => function ($row) {
                return Hui::primaryBtn('修改返点', ['editPoint', 'id' => $row->admin_id], ['class' => 'editBtn']);
            }]
        ], [
            'addBtn' => ['saveRetail' => '添加会员单位']
        ]);

        return $this->render('list', compact('html'));
    }


    /**
     * @authname 修改代理商返点%
     */
    public function actionEditPoint() 
    {
        $admin = AdminUser::findOne(get('id'));
        if (empty($admin)) {
            return error('查无此用户！');
        }
        if ($admin->power == AdminUser::POWER_MANAGER) {
            $aminLeader = AdminLeader::findOne($admin->pid);

            $retail = Retail::findOne($admin->id);
            $retail->point = post('point');
            $user = User::find()->joinWith(['userExtend'])->where(['admin_id' => $retail->admin_id, 'is_manager' => User::IS_MANAGER_YES])->orderBy('point DESC')->one();
            $point = 0;
            if (!empty($user)) {
                $point = $user->userExtend->point;
            }
            if (is_int($retail->point) || $retail->point < 0 || $retail->point > $aminLeader->point) {
                return error('代理商的返点不能大于上级的返点'.$aminLeader->point.'(设置返点为正整数)');
            }
            if ($aminLeader->point < $point) {
                return error('代理商的返点不能小于经纪人的返点('.$point.')！');
            }
            $retail->update(false);
            return success();
        } else {
           return error('非法参数！'); 
        }
    }

    /**
     * @authname 添加/编辑会员单位
     */
    public function actionSaveRetail($id = 0)
    {
        $model = Retail::findModel($id);
        $adminUser = new AdminUser;

        if ($model->load()) {
            $model->code = StringHelper::random(6, 'n');
            $model->admin_id = rand(1000, 9999);
            if ($model->validate()) {
                if ($model->file1) {
                    $model->file1->move();
                    $model->id_card = $model->file1->filePath;
                }
                if ($model->file2) {
                    $model->file2->move();
                    $model->paper = $model->file2->filePath;
                }
                if ($model->file3) {
                    $model->file3->move();
                    $model->paper2 = $model->file3->filePath;
                }
                if ($model->file4) {
                    $model->file4->move();
                    $model->paper3 = $model->file4->filePath;
                }
                $model->save(false);
                $admin = new AdminUser;
                $admin->username = $model->account;
                $admin->password = $model->pass;
                $admin->realname = $model->realname;
                $adminUser = req('AdminUser');
                $admin->pid = isset($adminUser['pid']) ? $adminUser['pid'] : u()->id;
                if ($admin->saveAdmin()) {
                    $auth = Yii::$app->authManager;
                    $role = $auth->getRole('代理商管理');
                    $auth->assign($role, $admin->id);
                    $admin->power = AdminUser::POWER_MANAGER;
                    $admin->update(false);
                    $model->admin_id = $admin->id;
                    $model->update();
                } else {
                    $model->delete();
                    return error($admin);
                }
                return success();
            } else {
                return error($model);
            }
        }

        return $this->render('saveRetail', compact('model', 'adminUser'));
    }

    /**
     * @authname 代理商出金列表
     */
    public function actionWithdrawList()
    {
        $query = (new AdminWithdraw)->listQuery()->orderBy('adminWithdraw.created_at DESC');
        $countQuery = (new AdminWithdraw)->listQuery()->andWhere(['op_state' => AdminWithdraw::OP_STATE_PASS]);

        $count = $countQuery->select('SUM(amount) amount')->one()->amount ?: 0;
        $html = $query->getTable([
            'admin_id',
            'retail.account',
            'retail.total_fee' => '账户余额',
            'retail.tel',
            'amount' => '出金金额',
            'created_at',
            'op_state' => ['search' => 'select'],
            u()->power < AdminUser::POWER_ADMIN?:['header' => '操作', 'width' => '70px', 'value' => function ($row) {
                if ($row['op_state'] == AdminWithdraw::OP_STATE_WAIT) {
                    return Hui::primaryBtn('会员出金', ['retail/verifyWithdraw', 'id' => $row['id']], ['class' => 'layer.iframe']);
                } else {
                    return Html::successSpan('已审核');
                }
            }]
        ], [
            'searchColumns' => [
                'admin_id',
                'retail.account',
                // 'time' => ['header' => '审核时间', 'type' => 'dateRange']
            ],
            'ajaxReturn' => [
                'count' => $count
            ],
            'addBtn' => u()->power >= AdminUser::POWER_ADMIN?'':['saveWithdraw' => '代理商申请出金']
        ]);
        

        return $this->render('withdrawList', compact('html', 'count'));
    }
     /**
     * @authname 经纪人出金列表
     */
    public function actionExuserWithdrawList()
    {
        $query = (new ExuserWithdraw)->listQuery()->orderBy('ExuserWithdraw.created_at DESC');
        $countQuery = (new ExuserWithdraw)->listQuery()->andWhere(['op_state' => ExuserWithdraw::OP_STATE_PASS]);

        $count = $countQuery->select('SUM(amount) amount')->one()->amount ?: 0;
        $html = $query->getTable([
            'user_id',
            'userExtend.realname',
            'userExtend.rebate_account' => '账户余额',
            'userExtend.mobile',
            'amount' => '出金金额',
            'created_at',
            'op_state' => ['search' => 'select'],
            u()->power < AdminUser::POWER_ADMIN?:['header' => '操作', 'width' => '70px', 'value' => function ($row) {
                if ($row['op_state'] == ExuserWithdraw::OP_STATE_WAIT) {
                    return Hui::primaryBtn('会员出金', ['retail/verifyExWithdraw', 'id' => $row['id']], ['class' => 'layer.iframe']);
                } else {
                    return Html::successSpan('已审核');
                }
            }]
        ], [
            'searchColumns' => [
                'user_id',
                'userExtend.realname',
                // 'time' => ['header' => '审核时间', 'type' => 'dateRange']
            ],
            'ajaxReturn' => [
                'count' => $count
            ],
            //'addBtn' => u()->power >= AdminUser::POWER_ADMIN?'':['saveWithdraw' => '代理商申请出金']
        ]);
        

        return $this->render('exwithdrawList', compact('html', 'count'));
    }

    /**
     * @authname 添加/编辑代理商出金
     */
    public function actionSaveWithdraw()
    {
        $model = new AdminWithdraw(['scenario' => 'withdraw']);

        $retail = Retail::find()->with('adminUser')->where(['admin_id' => u()->id])->one();
        if (empty($retail)) {
            return error('超管不能申请出金！');
        }
        $adminAccount = AdminAccount::findOne($retail->admin_id);
        if (empty($adminAccount)) {
            $adminAccount = new AdminAccount();
        }

        if ($model->load() || $adminAccount->load()) {
            if ($model->amount < 0 || $model->amount > $retail->total_fee) {
                return error('取现金额不能超过您的可用余额(非法参数)！');
            }
            $model->admin_id = $adminAccount->admin_id = $retail->admin_id;
            if ($model->validate()) {
                $adminAccount->attributes = post('AdminAccount');
                $adminAccount->id_card = 'xx';
                $adminAccount->realname = $adminAccount->bank_user;
                if ($adminAccount->validate()) {
                    $retail->total_fee = sprintf('%.2f', $retail->total_fee - $model->amount);
                    $retail->update();
                    $model->save(false);
                    $adminAccount->save(false);
                    return success();
                } else {
                   return error($adminAccount); 
                }
            } else {
                return error($model);
            }
        }

        return $this->render('saveWithdraw', compact('model', 'retail', 'adminAccount'));
    }

    /**
     * @authname 代理商出金操作
     */
    public function actionVerifyWithdraw($id)
    {
        $model = AdminWithdraw::find()->with(['retail.adminAccount'])->where(['id' => $id])->one();
        //var_dump($model);
        //exit;

        if (req()->isPost) {
            $model->op_state = post('state');
            if ($model->update()) {
                if ($model->op_state == AdminWithdraw::OP_STATE_DENY) {
                    $model->retail->total_fee += $model->amount;
                    $model->retail->update();    
                }
                return success();
            } else {
                return error($model);
            }
        }

        return $this->render('verifyWithdraw', compact('model'));
    }
    /**
     * @authname 经纪人出金操作
     */
    public function actionVerifyExWithdraw($id)
    {
        $model = ExuserWithdraw::find()->with(['userExtend'])->where(['id' => $id])->one();
        //var_dump($model);
        //exit;

        if (req()->isPost) {
            $model->op_state = post('state');
            if ($model->update()) {
                if ($model->op_state == ExuserWithdraw::OP_STATE_DENY) {
                    $model->userExtend->rebate_account += $model->amount;
                    $model->userExtend->update();    
                }
                return success();
            } else {
                return error($model);
            }
        }

        return $this->render('verifyExWithdraw', compact('model'));
    }
}
