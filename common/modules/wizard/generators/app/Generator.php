<?php

namespace common\modules\wizard\generators\app;

use Yii;

/**
 * 项目生成器
 *
 * @author ChisWill
 */
class Generator extends \common\modules\wizard\Generator
{
    // 虚拟字段
    public $appName;

    public function rules()
    {
        return [
            [['appName'], 'required', 'message' => '{attribute} 不能为空~！'],
            [['appName'], 'filter', 'filter' => 'trim'],
            [['appName'], 'validateAppName']
        ];
    }

    public function attributeLabels()
    {
        return [
            'appName' => '项目名'
        ];
    }

    /**
     * Validates the [[appName]] attribute.
     */
    public function validateAppName()
    {
        if ($this->isReservedKeyword($this->appName)) {
            $this->addError('appName', "项目名不能使用PHP保留的关键字.");
        } elseif (!preg_match('/^[a-z][a-z0-9]*$/', $this->appName)) {
            $this->addError('appName', "项目名不合法.");
        } else {
            try {
                Yii::getAlias('@' . $this->appName);
                $this->addError('appName', "该项目已经存在！");
            } catch (\yii\base\InvalidParamException $e) {
                // do nothing.
            }
        }
    }

    /**
     * 设置项目别名配置
     */
    public function setAppAlias()
    {
        $alias ="Yii::setAlias('{$this->appName}', dirname(dirname(__DIR__)) . '/{$this->appName}');";
        $content = preg_replace('/(Yii::setAlias\(\'@?common\',.*\);)/U', '$1' . PHP_EOL . $alias, file_get_contents(Yii::getAlias('@common/config/bootstrap.php')));
        file_put_contents(Yii::getAlias('@common/config/bootstrap.php'), $content);
        Yii::setAlias($this->appName, Yii::getAlias('@common/../') . $this->appName);
    }

    /**
     * 重置项目别名配置
     */
    public function revertAppAlias()
    {
        $content = preg_replace('/(Yii::setAlias\(\'@?common\',.*\);)\s*Yii::setAlias\(\'@?' . $this->appName . '\',.*\);/U', '$1', file_get_contents(Yii::getAlias('@common/config/bootstrap.php')));
        file_put_contents(Yii::getAlias('@common/config/bootstrap.php'), $content);
    }
}
