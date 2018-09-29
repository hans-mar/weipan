<?php

namespace common\models;

use Yii;

/**
 * 这是表 `retail` 的模型
 */
class Retail extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['admin_id', 'account', 'pass', 'company_name', 'realname'], 'required'],
            [['admin_id', 'point', 'created_by'], 'integer'],
            [['total_fee'], 'number'],
            [['created_at'], 'safe'],
            [['account', 'pass', 'tel', 'qq'], 'string', 'max' => 20],
            [['company_name', 'realname'], 'string', 'max' => 50],
            [['id_card', 'paper', 'paper2', 'paper3', 'code'], 'string', 'max' => 100],
            [['code'], 'unique']
        ];
    }

    public function attributeLabels()
    {
        return [
            'admin_id' => '账号id',
            'account' => '登录账号',
            'pass' => '登录密码',
            'company_name' => '会员单位名称',
            'realname' => '法人名称',
            'point' => '返点百分比%',
            'total_fee' => '手续费总计',
            'tel' => '联系电话',
            'qq' => 'QQ',
            'id_card' => '法人身份证',
            'paper' => '营业执照',
            'paper2' => '组织机构代码证',
            'paper3' => '税务登记证',
            'code' => '邀请码',
            'created_at' => '创建时间',
            'created_by' => 'Created By',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getAdminUser()
    {
        return $this->hasOne(AdminUser::className(), ['username' => 'account']);
    }

    public function getAdminAccount()
    {
        return $this->hasOne(AdminAccount::className(), ['admin_id' => 'admin_id']);
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'retail.admin_id' => $this->admin_id,
                'retail.point' => $this->point,
                'retail.total_fee' => $this->total_fee,
                'retail.created_by' => $this->created_by,
            ])
            ->andFilterWhere(['like', 'retail.account', $this->account])
            ->andFilterWhere(['like', 'retail.pass', $this->pass])
            ->andFilterWhere(['like', 'retail.company_name', $this->company_name])
            ->andFilterWhere(['like', 'retail.realname', $this->realname])
            ->andFilterWhere(['like', 'retail.tel', $this->tel])
            ->andFilterWhere(['like', 'retail.qq', $this->qq])
            ->andFilterWhere(['like', 'retail.id_card', $this->id_card])
            ->andFilterWhere(['like', 'retail.paper', $this->paper])
            ->andFilterWhere(['like', 'retail.paper2', $this->paper2])
            ->andFilterWhere(['like', 'retail.paper3', $this->paper3])
            ->andFilterWhere(['like', 'retail.code', $this->code])
            ->andFilterWhere(['like', 'retail.created_at', $this->created_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
