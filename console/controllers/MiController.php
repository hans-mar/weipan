<?php

namespace console\controllers;

use Yii;
use common\helpers\FileHelper;

/**
 * Custom migration application.
 */
class MiController extends \common\components\ConsoleController
{
    public function actionIndex($appName = null)
    {
        if ($appName) {
            $this->migrate($appName);
            return;
        }

        echo 'This is a customized Migration Tool.' . "\n\n";

        echo 'Here are all application: ' . implode(', ', FileHelper::getApps(['common', 'console', 'api'])) . ".\n\n";

        echo 'You could use command `yii mi frontend` to execute.' . "\n";
    }

    /**
     * Upgrades all versions.
     */
    public function migrate($appName)
    {
        $generator = new \common\modules\wizard\generators\migrate\Generator;
        try {
            $basePath = Yii::getAlias('@' . $appName);
        } catch (\yii\base\InvalidParamException $e) {
            die($e->getMessage() . "\n");
        }
        if (!file_exists($basePath . '/' . $generator->saveFile)) {
            die('This application has no migration.' . "\n");
        } else {
            $generator->appName = $appName;
            list($successNum, $err) = $generator->syncAll();
            $successInfo = $successNum === 0 ? 'Nothing is Upgraded.' : "upgrade {$successNum} version.";
            $errInfo = $err ? "\nErrors:\n" . implode("\n", $err) : '';
            if ($successNum) {
                echo $successInfo . "\n";
            } else {
                echo $successInfo, $errInfo . "\n";
            }
        }
    }
}
