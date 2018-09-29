<?php

namespace common\models;

use Yii;

/**
 * 这是表 `hsh_option` 的模型
 */
class Option extends \common\components\ARModel
{
    const TYPE_COMMON = 1;
    const TYPE_SYSTEM = 2;

    public function rules()
    {
        return [
            [['option_value'], 'default', 'value' => ''],
            [['type', 'state'], 'integer'],
            [['option_name'], 'string', 'max' => 64],
            [['option_name'], 'unique']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'option_name' => '配置名',
            'option_value' => '配置值',
            'type' => '配置类型',
            'state' => 'State',
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
                'option.id' => $this->id,
                'option.type' => $this->type,
                'option.state' => $this->state,
            ])
            ->andFilterWhere(['like', 'option.option_name', $this->option_name])
            ->andFilterWhere(['like', 'option.option_value', $this->option_value])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/


    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
