<?php

namespace console\components;

use common\helpers\System;

/**
 * 本类的目的是让 workerman 兼容 Yii2 的 控制台执行方式
 * 由于同时集成了 Windows 版和 Linux 版的workerman
 * 为了简化使用，还增加了自动判断功能，在当前系统中会自动使用正确的版本
 *
 * @author ChisWill
 */
class Worker extends \Workerman\Worker
{
    /**
     * Run all worker instances. 
     *
     * @return void
     */
    public static function runAll()
    {
        if (System::isWindowsOs()) {
            parent::runAll();
        } else {
            self::checkSapiEnv();
            self::init();
            self::parseCommand();
            self::daemonize();
            self::initWorkers();
            self::installSignal();
            self::saveMasterPid();
            self::forkWorkers();
            self::displayUI();
            self::resetStd();
            self::monitorWorkers();
        }
    }

    /**
     * Parse command.
     * origin: php yourfile.php start | stop | restart | reload | status
     * now: yii socket start
     *
     * @return void
     */
    protected static function parseCommand()
    {
        global $argv;
        
        if (!System::isWindowsOs()) {
            if (substr($argv[0], -3, 3) === 'yii') {
                $runPath = array_shift($argv);
                $name = array_shift($argv);
                $runPath = substr($runPath, 0, -3);
                $filePath = $runPath . 'console' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . ucfirst($name) . 'Controller.php';
                array_unshift($argv, $filePath);
            }
        }
        parent::parseCommand();
    }
}
