<?php

namespace common\modules\wizard\generators\module;

use Yii;

/**
 * 模块生成器
 *
 * @author ChisWill
 */
class Generator extends \common\modules\wizard\Generator
{
    // 虚拟字段
    public $moduleName;
    public $moduleNamespace;
    public $moduleApp;

    public function rules()
    {
        return [
            [['moduleName'], 'required', 'message' => '{attribute} 不能为空~！'],
            [['moduleName', 'moduleNamespace'], 'filter', 'filter' => 'trim'],
            [['moduleName'], 'validateModuleName'],
            [['moduleName'], 'validateModuleNamespace'],
            [['moduleApp'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'moduleName' => '模块名',
            'moduleApp' => '项目'
        ];
    }

    /**
     * Validates the [[moduleName]] attribute.
     */
    public function validateModuleName()
    {
        if ($this->isReservedKeyword($this->moduleName)) {
            $this->addError('moduleName', "模块名不能使用PHP保留的关键字.");
        } elseif (!preg_match('/^[a-z][a-z0-9]*$/', $this->moduleName)) {
            $this->addError('moduleName', "模块名不合法.");
        }
    }

    /**
     * Validates the [[moduleNamespace]] attribute.
     */
    public function validateModuleNamespace()
    {
        if (!$this->moduleNamespace && !$this->moduleApp) {
            $this->addError('moduleNamespace', '项目与命名空间必须选择填一个~！');
            return;
        }

        if ($this->moduleApp) {
            $this->moduleNamespace = $this->moduleApp . '\modules';
        } 
        // 校验命名空间是否合法
        $this->moduleNamespace = trim(str_replace('/', '\\', $this->moduleNamespace), '\\');
        try {
            $path = Yii::getAlias('@' . explode('\\', $this->moduleNamespace)[0]);
        } catch (\yii\base\InvalidParamException $e) {
            $this->addError('moduleNamespace', $e->getMessage());
            return;
        }

        $path .= '/modules/' . $this->moduleName;
        // 校验将创建的模块是否存在
        if (file_exists($path)) {
            $this->addError('moduleNamespace', '该模块已经存在~！');
        }
    }

    /**
     * 更新配置文件
     */
    public function updateConfig()
    {
        $app = explode('\\', $this->moduleNamespace)[0];
        $content = file_get_contents(Yii::getAlias("@{$app}/config/main.php"));
        preg_match('/return\s*\[.*\'modules\'\s*=>\s*(\[.*\]).*\]\s*;/Us', $content, $match);
        if (isset($match[1])) {
            eval("\$modules = $match[1];");
            $config = '[' . PHP_EOL;
            foreach ($modules as $key => $value) {
                $config .= str_repeat(' ', 8) . "'{$key}' => '{$value}'," . PHP_EOL;
            }
            $config .= str_repeat(' ', 8) . $this->getModuleConfig() . PHP_EOL . str_repeat(' ', 4) . ']';
            $content = preg_replace('/(return\s*\[.*\'modules\'\s*=>\s*)(\[.*\])(.*\]\s*;)/Us', '$1' . $config . '$3', $content);
            file_put_contents(Yii::getAlias("@{$app}/config/main.php"), $content);
        } else {
            $config = PHP_EOL . str_repeat(' ', 4) . '\'modules\' => [' . PHP_EOL . str_repeat(' ', 8) . $this->getModuleConfig() . PHP_EOL . str_repeat(' ', 4) . '],';
            $content = preg_replace('/(return\s*\[)(.*\]\s*;)/Us', '$1' . $config . '$2', $content);
            file_put_contents(Yii::getAlias("@{$app}/config/main.php"), $content);
        }
    }

    private function getModuleConfig()
    {
        return "'{$this->moduleName}' => '{$this->moduleNamespace}\\{$this->moduleName}\\Module'";
    }
}
