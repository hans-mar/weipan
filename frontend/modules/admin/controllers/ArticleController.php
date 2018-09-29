<?php

namespace admin\controllers;

use Yii;
use admin\models\Article;
use common\helpers\Html;

class ArticleController extends \admin\components\Controller
{
    /**
     * @authname 资讯列表
     */
    public function actionList()
    {
        $query = Article::find()->orderBy('id DESC');

        $html = $query->getTable([
            'id',
            'title',
            'content',
            'publish_time',
            ['type' => ['edit' => 'saveArticle', 'delete' => 'deleteArticle']]
        ], [
            'addBtn' => ['saveArticle' => '添加新闻']
        ]);

        return $this->render('list', compact('html'));
    }

    /**
     * @authname 添加/编辑资讯
     */
    public function actionSaveArticle($id = 0)
    {
        $model = Article::findModel($id);

        if ($model->load(post())) {
            if ($model->save()) {
                return success();
            } else {
                return error($model);
            }
        }

        return $this->render('saveArticle', compact('model'));
    }

    /**
     * @authname 删除资讯
     */
    public function actionDeleteArticle()
    {
        return parent::actionDelete();
    }
}
