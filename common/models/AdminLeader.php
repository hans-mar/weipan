<?php

namespace common\models;

use Yii;

/**
 * 这是表 `admin_leader` 的模型
 */
class AdminLeader extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['admin_id'], 'required'],
            [['admin_id', 'point', 'state', 'created_by', 'updated_by'], 'integer'],
            [['deposit'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['mobile'], 'string', 'max' => 11]
        ];
    }

    public function attributeLabels()
    {
        return [
            'admin_id' => 'Admin ID',
            'mobile' => '手机号',
            'point' => '返点百分比%',
            'deposit' => '保证金',
            'state' => 'State',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getAdmin()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'admin_id']);
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'adminLeader.admin_id' => $this->admin_id,
                'adminLeader.point' => $this->point,
                'adminLeader.deposit' => $this->deposit,
                'adminLeader.state' => $this->state,
                'adminLeader.created_by' => $this->created_by,
                'adminLeader.updated_by' => $this->updated_by,
            ])
            ->andFilterWhere(['like', 'adminLeader.mobile', $this->mobile])
            ->andFilterWhere(['like', 'adminLeader.created_at', $this->created_at])
            ->andFilterWhere(['like', 'adminLeader.updated_at', $this->updated_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
