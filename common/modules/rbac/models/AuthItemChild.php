<?php

namespace common\modules\rbac\models;

use Yii;

/**
 * 这是表 `hsh_auth_item_child` 的模型
 */
class AuthItemChild extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['parent', 'child'], 'required', 'message' => '{attribute} 必填！'],
            [['parent', 'child'], 'string', 'max' => 64]
        ];
    }

    public function attributeLabels()
    {
        return [
            'parent' => '角色名',
            'child' => 'Child',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getChildItem()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'child'])->select(['name', 'type', 'description', 'rule_name', 'data']);
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->andFilterWhere(['like', 'authItemChild.parent', $this->parent])
            ->andFilterWhere(['like', 'authItemChild.child', $this->child])
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
