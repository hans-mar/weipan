<?php

namespace common\models;

use Yii;

/**
 * 这是表 `user` 的模型
 */
class User extends \common\components\ARModel
{
    const IS_MANAGER_YES = 1;
    const IS_MANAGER_NO = -1;

    const APPLY_STATE_NONE = 1;
    const APPLY_STATE_WAIT = 2;
    const APPLY_STATE_PASS = 3;
    const APPLY_STATE_DENY = -1;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['admin_id', 'pid', 'is_manager', 'state', 'apply_state', 'created_by', 'updated_by'], 'integer'],
            [['account', 'blocked_account', 'profit_account', 'loss_account', 'total_fee'], 'number'],
            [['login_time', 'created_at', 'updated_at'], 'safe'],
            [['username', 'invide_code'], 'string', 'max' => 20],
            [['password'], 'string', 'max' => 80],
            [['mobile'], 'string', 'max' => 11],
            [['nickname', 'open_id'], 'string', 'max' => 100],
            [['fee_detail'], 'string', 'max' => 250],
            [['face'], 'string', 'max' => 150],
            [['open_id'], 'unique']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'password' => '密码',
            'mobile' => '手机号',
            'nickname' => '昵称',
            'admin_id' => '代理商ID',
            'pid' => '邀请人ID',
            'invide_code' => '邀请码',
            'account' => '账户余额',
            'blocked_account' => '冻结金额',
            'profit_account' => '总盈利',
            'loss_account' => '总亏损',
            'total_fee' => '返点总额',
            'fee_detail' => '各级返点详情',
            'login_time' => '最后登录时间',
            'is_manager' => '是否是经纪人',
            'face' => '头像',
            'open_id' => '微信的open_id',
            'state' => 'State',
            'apply_state' => '申请状态：1未申请，2待审核，3审核通过，-1审核不通过',
            'created_at' => '注册时间',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getUserAccount()
    {
        return $this->hasOne(UserAccount::className(), ['user_id' => 'id']);
    }

    public function getAdmin()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'admin_id']);
    }

    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'pid']);
    }

    public function getUserExtend()
    {
        return $this->hasOne(UserExtend::className(), ['user_id' => 'id']);
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'user.id' => $this->id,
                'user.admin_id' => $this->admin_id,
                'user.pid' => $this->pid,
                'user.account' => $this->account,
                'user.blocked_account' => $this->blocked_account,
                'user.profit_account' => $this->profit_account,
                'user.loss_account' => $this->loss_account,
                'user.total_fee' => $this->total_fee,
                'user.is_manager' => $this->is_manager,
                'user.state' => $this->state,
                'user.apply_state' => $this->apply_state,
                'user.created_by' => $this->created_by,
                'user.updated_by' => $this->updated_by,
            ])
            ->andFilterWhere(['like', 'user.username', $this->username])
            ->andFilterWhere(['like', 'user.password', $this->password])
            ->andFilterWhere(['like', 'user.mobile', $this->mobile])
            ->andFilterWhere(['like', 'user.nickname', $this->nickname])
            ->andFilterWhere(['like', 'user.invide_code', $this->invide_code])
            ->andFilterWhere(['like', 'user.fee_detail', $this->fee_detail])
            ->andFilterWhere(['like', 'user.login_time', $this->login_time])
            ->andFilterWhere(['like', 'user.face', $this->face])
            ->andFilterWhere(['like', 'user.open_id', $this->open_id])
            ->andFilterWhere(['like', 'user.created_at', $this->created_at])
            ->andFilterWhere(['like', 'user.updated_at', $this->updated_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    public function hashPassword()
    {
        $this->password = Yii::$app->security->generatePasswordHash($this->password);

        return $this;
    }

    //获取经纪人的三级下线id
    public static function getUserOfflineId()
    {
        $idArr[0] = self::find()->where(['pid' => u()->id, 'state' => self::STATE_VALID])->map('id', 'id');
        $idArr[1] = self::find()->where(['in', 'pid', $idArr[0]])->andWhere(['state' => self::STATE_VALID])->map('id', 'id');
        return $idArr;
    }

    //获取返点数据的集合
    public static function getUserOfflineData($idArr = [])
    {
        //三级交易总额 返点金额
        foreach ($idArr as $k => $arr) {
            $data['amount'][$k] = Order::find()->where(['in', 'user_id', $arr])->andWhere('fee > 0')->select('SUM(deposit) AS deposit')->one()->deposit?:0;
            $data['rebate'][$k] = UserRebate::find()->where(['in', 'user_id', $arr])->andWhere(['pid' => u()->id])->select('SUM(amount) AS amount')->one()->amount?:0;
        }

        return $data;
    }

    //用户充值总额
    public static function getUserChargeAmount($user_id)
    {
        return UserCharge::find()->where(['charge_state' => UserCharge::CHARGE_STATE_PASS, 'user_id' => $user_id])->select('SUM(amount) AS amount')->one()->amount?:0;
    }

    //用户返点总额
    public static function getUserRebateAmount($user_id)
    {
        return UserRebate::find()->where(['pid' => $user_id])->select('SUM(amount) AS amount')->one()->amount?:0;
    }

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/

    // Map method of field `is_manager`
    public static function getIsManagerMap($prepend = false)
    {
        $map = [
            self::IS_MANAGER_YES => '是',
            self::IS_MANAGER_NO => '否'
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `is_manager`
    public function getIsManagerValue($value = null)
    {
        return $this->resetValue($value);
    }

    // Map method of field `apply_state`
    public static function getApplyStateMap($prepend = false)
    {
        $map = [
            self::APPLY_STATE_NONE => 1,
            self::APPLY_STATE_WAIT => 2,
            self::APPLY_STATE_PASS => 3,
            self::APPLY_STATE_DENY => -1,
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `apply_state`
    public function getApplyStateValue($value = null)
    {
        return $this->resetValue($value);
    }
}
