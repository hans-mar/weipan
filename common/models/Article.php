<?php

namespace common\models;

use Yii;

/**
 * 这是表 `article` 的模型
 */
class Article extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['title', 'content', 'publish_time'], 'required'],
            [['content'], 'default', 'value' => ''],
            [['publish_time', 'created_at', 'updated_at'], 'safe'],
            [['category', 'created_by', 'updated_by'], 'integer'],
            [['title'], 'string', 'max' => 50]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'publish_time' => '发生时间',
            'category' => '分类',
            'created_at' => '发布时间',
            'created_by' => '发布人',
            'updated_at' => '更新时间',
            'updated_by' => '修改人',
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
                'article.id' => $this->id,
                'article.category' => $this->category,
                'article.created_by' => $this->created_by,
                'article.updated_by' => $this->updated_by,
            ])
            ->andFilterWhere(['like', 'article.title', $this->title])
            ->andFilterWhere(['like', 'article.content', $this->content])
            ->andFilterWhere(['like', 'article.publish_time', $this->publish_time])
            ->andFilterWhere(['like', 'article.created_at', $this->created_at])
            ->andFilterWhere(['like', 'article.updated_at', $this->updated_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
