<?php

namespace common\modules\wizard\controllers;

use Yii;
use common\helpers\Cookie;
use common\helpers\Inflector;
use common\helpers\FileHelper;

/**
 * 以下是旧版代码，新版已经移植到 common\actions\GenerateAcion 中
 * 
 * @author ChisWill
 */
class GenerateController extends \common\components\WebController
{
    /**
     * 调用各生成生成器模块
     */
    public function actionCode()
    {
        $action = post('action');

        if (method_exists($this, $action)) {
            return call_user_func([$this, $action]);
        } else {
            die('Access Denied');
        }
    }

    /**
     * 生成项目
     */
    protected function generateApp()
    {
        $generator = $this->loadGenerator('app');
        if ($generator->hasErrors()) {
            return self::error($generator);
        }
        // 设置项目别名
        $generator->setAppAlias();
        // 获取项目目录
        $appPath = Yii::getAlias('@' . $generator->appName);
        try {
            ob_start();
            // 复制整个项目目录
            FileHelper::copyDirectory(Yii::getAlias('@common/modules/wizard/views/generate/app/'), $appPath);
            // 所有需要修改的模板路径
            $list = [
                'assets' => ['AppAsset', 'UserAsset'],
                'components' => ['Controller', 'WebUser'],
                'config' => ['main'],
                'controllers' => ['SiteController', 'UserController'],
                'models' => ['User']
            ];
            // 渲染视图的参数
            $params = [
                'appName' => $generator->appName
            ];
            // 修改所有模板
            foreach ($list as $space => $sub) {
                foreach ($sub as $class) {
                    $params['namespace'] = "{$generator->appName}\\$space";
                    $path = Yii::getAlias("@{$generator->appName}/{$space}/{$class}.php");
                    $content = $this->renderPartial("app/{$space}/{$class}", $params);
                    file_put_contents($path, $content);
                }
            }
            // 修改视图中布局文件中的命名空间名称
            $layoutPath = Yii::getAlias('@' . $generator->appName . '/views/layouts/main.php');
            $namespace = $generator->appName . '\\assets';
            file_put_contents($layoutPath, str_replace('$namespace', $namespace . '\\AppAsset', file_get_contents($layoutPath)));

            return self::success('项目 ' . $generator->appName . ' 生成成功~！');
        } catch (\Exception $e) {
            ob_end_clean();
            // 重置所做的操作
            FileHelper::removeDirectory($appPath);
            $generator->revertAppAlias();

            return self::error([
                '项目 ' . $generator->appName . ' 生成失败~！',
                '原因：' . $e->getMessage()
            ]);
        }
    }

    /**
     * 生成模块
     */
    protected function generateModule()
    {
        // 获取module生成器
        $generator = $this->loadGenerator('module');
        if ($generator->hasErrors()) {
            return self::error($generator);
        }
        // 获取模块目录
        $modulePath = Yii::getAlias('@' . trim(str_replace('\\', '/', $generator->moduleNamespace), '/')) . '/' . $generator->moduleName;
        try {
            ob_start();
            // 复制assets目录
            FileHelper::copyDirectory(Yii::getAlias('@common/modules/wizard/views/generate/module/assets'), $modulePath . '/assets');
            // 创建controllers目录
            FileHelper::mkdir($modulePath . '/controllers');
            // 创建默认controller
            $params = [
                'namespace' => $generator->moduleNamespace . '\\' . $generator->moduleName . '\\controllers',
                'moduleName' => $generator->moduleName
            ];
            $content = $this->renderPartial('module/controllers/controller', $params);
            file_put_contents($modulePath . '/controllers/SiteController.php', $content);
            // 创建models目录
            FileHelper::mkdir($modulePath . '/models');
            // 复制views目录
            $viewPath = Yii::getAlias('@common/modules/wizard/views/generate/module/views');
            FileHelper::copyDirectory($viewPath, $modulePath . '/views');
            // 修改视图中布局文件中的命名空间名称
            $layoutPath = $viewPath . '/layouts/main.php';
            $namespace = $generator->moduleNamespace . '\\' . $generator->moduleName;
            file_put_contents($modulePath . '/views/layouts/main.php', str_replace('$namespace', $namespace . '\\Asset', file_get_contents($layoutPath)));
            // 创建Asset文件
            $params = [
                'namespace' => $namespace,
                'moduleName' => $generator->moduleName,
                'sourcePath' => '@' . str_replace('\\', '/', $namespace) . '/assets'
            ];
            $content = $this->renderPartial('module/Asset', $params);
            file_put_contents($modulePath . '/Asset.php', $content);
            // 创建Module文件
            $content = $this->renderPartial('module/Module', $params);
            file_put_contents($modulePath . '/Module.php', $content);
            // 修改对应项目的main.php文件
            $generator->updateConfig();

            return self::success('模块 ' . $generator->moduleName . ' 生成成功~！');
        } catch (\Exception $e) {
            ob_end_clean();
            // 重置所做的操作
            FileHelper::removeDirectory($modulePath);

            return self::error([
                '模块 ' . $generator->moduleName . ' 生成失败~！',
                '原因：' . $e->getMessage()
            ]);
        }
    }

    /**
     * 同步数据库迁移
     */
    protected function generateMigrate()
    {
        // 获取migrate生成器
        $generator = Yii::createObject($this->module->generators['migrate']);
        // 执行所有待更新迁移文件
        list($successNum, $err) = $generator->syncAll();
        $successInfo = $successNum === 0 ? '没有记录被更新。' : "成功同步 {$successNum} 条数据。";
        $errInfo = $err ? "\n遭遇一个错误\n" . implode("\n", $err) : '';
        if ($successNum) {
            return self::success($successInfo);
        } else {
            return self::error($successInfo . $errInfo);
        }
    }

    /**
     * 生成字段的格式化方法
     */
    protected function generateField()
    {
        // 获取field生成器
        $generator = $this->loadGenerator('field');
        if ($generator->hasErrors()) {
            return self::error($generator);
        }
        // 获取文件内容
        $fileContent = file_get_contents($generator->modelPath);
        // 设置视图的参数
        $params = [
            'field' => $generator->fieldName,
            'methodName' => Inflector::camelize($generator->fieldName)
        ];
        // 获取渲染的代码
        $content = $this->renderPartial('field', $params);
        // 替换源文件的内容
        $newContent = preg_replace('/{([\s\S]*)}/i', "{" . '$1' . $content . "}", $fileContent);
        // 写入文件
        if (@file_put_contents($generator->modelPath, $newContent)) {
            return self::success($generator->fullModelName . ' 的字段 ' . $generator->fieldName . ' 的格式化方法生成成功~！');
        } else {
            return self::error($generator->fullModelName . ' 的字段 ' . $generator->fieldName . ' 的格式化方法生成失败！！文件无法写入！！');
        }
    }

    /**
     * 模型生成
     */
    protected function generateModel()
    {
        // 获取model生成器
        $generator = $this->loadGenerator('model');
        if ($generator->hasErrors()) {
            return self::error($generator);
        }
        // 获取数据库连接
        $db = $generator->getDbConnection();
        // 获取完整表名
        $fullTableName = $db->tablePrefix . $generator->tableName;
        // 获取模型名
        $modelClassName = $generator->generateClassName($generator->alias);
        // 获取表信息
        $tableSchema = $db->getTableSchema($fullTableName);
        // 如果项目选择 common 表示在公共、和当前项目一起生成模型，否则仅生成指定项目或命名空间的模型
        if ($generator->application === 'common') {
            $modelPath = Yii::getAlias('@' . str_replace('\\', '/', $generator->mainNamespace));
            $namespace = $generator->mainNamespace;
            $subNamespaces = [FileHelper::getCurrentApp() . '\models'];
        } else {
            $modelPath = Yii::getAlias('@' . str_replace('\\', '/', $generator->modelNamespace));
            $namespace = $generator->modelNamespace;
            $subNamespaces = [];
        }
        // 设置视图的参数
        $mainParams = [
            'generator' => $generator,
            'namespace' => $namespace,
            'tableName' => $fullTableName,
            'className' => $modelClassName,
            'alias' => lcfirst($modelClassName),
            'labels' => $generator->generateLabels($tableSchema),
            'rules' => $generator->generateRules($tableSchema),
            'compares' => $generator->generateCompares($tableSchema)
        ];
        if (!file_exists($modelPath)) {
            FileHelper::mkdir($modelPath);
        }
        // 生成代码并写入文件
        $filename = $modelPath . '/' . $modelClassName . '.php';
        if (!file_exists($filename)) {
            if (@file_put_contents($filename, $this->renderPartial('model', $mainParams))) {
                $msg = [$namespace . '\\' . $modelClassName . ' 生成成功~！'];
            } else {
                $msg = [$namespace . '\\' . $modelClassName . ' 生成失败~！'];
            }
        } else {
            // 当文件存在时，只替换指定内容
            $oldContent = file_get_contents($filename);
            $newContent = $this->renderPartial('model', $mainParams);
            $replaceMethods = ['rules', 'attributeLabels', 'search'];
            array_walk($replaceMethods, function ($method) use (&$oldContent, $newContent) {
                $pattern = '/public\s*function\s*' . $method . '\(\).*{.*}/Uis';
                preg_match($pattern, $newContent, $match);
                $oldContent = preg_replace($pattern, $match[0], $oldContent);
            });
            if (@file_put_contents($filename, $oldContent)) {
                $msg = [$namespace . '\\' . $modelClassName . ' 覆盖成功~！'];
            } else {
                $msg = [$namespace . '\\' . $modelClassName . ' 覆盖失败~！'];
            }
        }
        // 生成附属的模型
        foreach ($subNamespaces as $subNs) {
            $subPath = Yii::getAlias('@' . str_replace('\\', '/', $subNs));
            if (!file_exists($subPath)) {
                FileHelper::mkdir($subPath);
            }
            $fileName = $subPath . '/' . $modelClassName . '.php';
            if (file_exists($fileName)) {
                continue;
            }
            $subNamespace = str_replace('/', '\\', $subNs);
            $subParams = [
                'generator' => $generator,
                'namespace' => $subNamespace,
                'tableName' => $fullTableName,
                'className' => $modelClassName,
                'parentClass' => '\\' . $namespace . '\\' . $modelClassName
            ];
            if (@file_put_contents($fileName, $this->renderPartial('subModel', $subParams))) {
                $msg[] = $subNamespace . '\\' . $modelClassName . ' 生成成功~！';
            } else {
                $msg[] = $subNamespace . '\\' . $modelClassName . ' 生成失败~！';
            }
        }
        return self::success($msg);
    }

    /**
     * Loads the generator with the specified ID.
     * @param string $id the ID of the generator to be loaded.
     * @return the loaded generator
     * @throws NotFoundHttpException
     */
    protected function loadGenerator($id)
    {
        if (isset($this->module->generators[$id])) {
            $generator = Yii::createObject($this->module->generators[$id]);
            $generator->attributes = post('Wizard');
            $generator->validate();
            return $generator;
        } else {
            throwex("Code generator not found: $id");
        }
    }
}
