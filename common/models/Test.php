<?php

namespace common\models;

use Yii;

/**
 * 这是表 `test` 的模型
 */
class Test extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['mobile', 'state', 'created_by'], 'integer'],
            [['message'], 'default', 'value' => ''],
            [['reg_date', 'created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 20]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'title' => 'Title',
            'message' => 'Message',
            'reg_date' => 'Reg Date',
            'state' => 'State',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
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
                'test.id' => $this->id,
                'test.mobile' => $this->mobile,
                'test.state' => $this->state,
                'test.created_by' => $this->created_by,
            ])
            ->andFilterWhere(['like', 'test.name', $this->name])
            ->andFilterWhere(['like', 'test.title', $this->title])
            ->andFilterWhere(['like', 'test.message', $this->message])
            ->andFilterWhere(['like', 'test.reg_date', $this->reg_date])
            ->andFilterWhere(['like', 'test.created_at', $this->created_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
