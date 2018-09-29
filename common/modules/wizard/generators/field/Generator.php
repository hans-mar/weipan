<?php

namespace common\modules\wizard\generators\field;

use Yii;
use yii\helpers\Inflector;

/**
 * 该生成器将会生成模型中，字段对应的格式化方法
 *
 * @author ChisWill
 */
class Generator extends \common\modules\wizard\Generator
{
    // 虚拟字段
    public $modelName;
    public $fieldName;
    public $modelPath;
    public $fullModelName;
    // 默认配置设定
    public $namespace = 'common\models';

    public function rules()
    {
        return [
            [['modelName', 'fieldName'], 'required', 'message' => '{attribute} 不能为空~!'],
            [['modelName', 'fieldName'], 'filter', 'filter' => 'trim'],
            [['modelName'], 'validateModelName']
        ];
    }

    public function attributeLabels()
    {
        return [
            'modelName' => '模型名',
            'fieldName' => '字段名'
        ];
    }

    /**
     * 验证模型是否存在
     */
    public function validateModelName()
    {
        // 判断是否是完整类名
        if (strpos($this->modelName, '\\') !== false) {
            $this->fullModelName = $this->modelName;
        } else {
            $this->fullModelName = $this->namespace . '\\' . ucfirst($this->modelName);
        }
        if (!class_exists($this->fullModelName)) {
            $this->addError('modelName', "类名 '" . $this->fullModelName . "' 不存在~！");
        } else {
            $this->modelPath = Yii::getAlias('@' . str_replace('\\', '/', $this->fullModelName) . '.php');
        }
    }
}
