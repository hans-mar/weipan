<?php

namespace common\widgets;

use common\helpers\Hui;
use common\helpers\Html;

/**
 * 加强 ActiveForm 的功能，增加公共的快捷方法
 * 
 * @author ChisWill
 */
class ActiveForm extends \yii\widgets\ActiveForm
{
    /**
     * @var array 提交按钮的行属性
     */
    public $submitRowOptions = [];

    /**
     * 快捷生成带html结构的表单提交按钮，参数采纳了向前补齐策略
     *
     * @param object|string $label   如果传入模型，将会自动判断提交按钮描述，否则就是固定字符串按钮名称
     * @param array         $options 按钮属性
     * @see common\helpers\Hui::submitInput()
     */
    public function submit($label = '确定并提交', $options = [])
    {
        if ($label instanceof \yii\db\ActiveRecord) {
            $label = $label->isNewRecord ? '创建' : '修改' ;
        }
        $options['id'] = empty($options['id']) ? 'submitBtn' : $options['id'];
        $submit = Hui::successSubmitInput($label, $options);
        return Html::tag('div', $submit, $this->submitRowOptions);
    }
}
