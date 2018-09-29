<?php

namespace admin\behaviors;

use Yii;
use common\helpers\Html;

/**
 * 后台模块视图层额外方法
 *
 * @author ChisWill
 */
class ViewBehavior extends \yii\base\Behavior
{
    /**
     * 后台模块生成tab栏模板
     * 
     * @param  array   $titleList  tab栏标题序列
     * @param  array   $actionList 与tab栏标题对应的action序列
     * @return string
     */
    public function tab($titleList, $actionList)
    {
        $view = $this->owner;

        $barHtml = '';
        $firstOptions = ['class' => 'current'];
        foreach ($titleList as $title) {
            $barHtml .= Html::span($title, $firstOptions);
            $firstOptions = [];
        }
        $barHtml = Html::tag('div', $barHtml, ['class' => 'tabBar cl']);
        $contentHtml = '';
        foreach ($actionList as $action) {
            $action = 'action' . ucfirst($action);
            $contentHtml .= Html::tag('div', $view->context->$action(), ['class' => 'tabCon']);
        }

        $view->registerJs('$.tab()');
        
        return Html::tag('div', $barHtml . $contentHtml, ['class' => 'tab-container']);
    }
}
