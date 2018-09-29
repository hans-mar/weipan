<?php

namespace common\modules\rbac\models;

use Yii;

/**
 * 这是表 `hsh_auth_rule` 的模型
 */
class AuthRule extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['data'], 'default', 'value' => ''],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 64]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    // public function getRelation()
    // {
    //     return $this->hasOne(Class::className(), ['id' => 'target_id']);
    // }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'authRule.created_at' => $this->created_at,
                'authRule.updated_at' => $this->updated_at,
                ])
            ->andFilterWhere(['like', 'authRule.name', $this->name])
            ->andFilterWhere(['like', 'authRule.data', $this->data])
        ;
    }

    // public function indexSearch()
    // {
    //    $query = $this->search();
    //
    //    return $query;
    // }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
