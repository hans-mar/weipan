<?php

namespace common\modules\rbac\models;

use Yii;

/**
 * 这是表 `hsh_auth_assignment` 的模型
 */
class AuthAssignment extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['created_at'], 'integer'],
            [['item_name', 'user_id'], 'string', 'max' => 64]
        ];
    }

    public function attributeLabels()
    {
        return [
            'item_name' => 'Item Name',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getItem()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'item_name']);
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'authAssignment.created_at' => $this->created_at,
                ])
            ->andFilterWhere(['like', 'authAssignment.item_name', $this->item_name])
            ->andFilterWhere(['like', 'authAssignment.user_id', $this->user_id])
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
