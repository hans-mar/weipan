<?php

namespace common\models;

use Yii;

/**
 * 这是表 `user_extend` 的模型
 */
class UserExtend extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['user_id', 'realname'], 'required'],
            [['user_id', 'point', 'coding', 'state', 'created_by', 'updated_by'], 'integer'],
            [['rebate_account'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['realname'], 'string', 'max' => 20],
            [['mobile'], 'string', 'max' => 11]
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'realname' => '真实姓名',
            'mobile' => '手机号',
            'point' => '返点百分比%',
            'rebate_account' => '返佣金额',
            'coding' => '机构编码或微圈编码',
            'state' => 'State',
            'created_at' => '注册时间',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    // public function getRelation()
    // {
    //     return $this->hasOne(Class::className(), ['foreign_key' => 'primary_key']);
    // }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'userExtend.user_id' => $this->user_id,
                'userExtend.point' => $this->point,
                'userExtend.rebate_account' => $this->rebate_account,
                'userExtend.coding' => $this->coding,
                'userExtend.state' => $this->state,
                'userExtend.created_by' => $this->created_by,
                'userExtend.updated_by' => $this->updated_by,
            ])
            ->andFilterWhere(['like', 'userExtend.realname', $this->realname])
            ->andFilterWhere(['like', 'userExtend.mobile', $this->mobile])
            ->andFilterWhere(['like', 'userExtend.created_at', $this->created_at])
            ->andFilterWhere(['like', 'userExtend.updated_at', $this->updated_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
