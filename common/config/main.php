<?php
/**
 * 共通配置项
 */
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'zh-CN',
    'timeZone'=>'Asia/Shanghai',
    'bootstrap' => bootstrap_filter(['rbac', 'test', 'setting'], ['wizard']),
    'modules' => [
        'rbac' => 'common\modules\rbac\Module',
        'test' => 'common\modules\test\Module',
        'wizard' => 'common\modules\wizard\Module',
        'setting' => 'common\modules\setting\Module'
    ],
    'components' => [
        'user' => [
            'enableAutoLogin' => true,
            'enableSession' => true,
            'idParam' => '__user'
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache'
        ],
        'urlManager' => [
            'showScriptName' => false,
            'enablePrettyUrl' => true
        ],
        'session' => [
            'class' => 'common\components\Session'
        ],
        'view' => [
            'theme' => THEME_NAME ? [
                'basePath' => '@frontend/themes/' . THEME_NAME,
                'baseUrl' => '@web/themes/' . THEME_NAME,
                'pathMap' => [
                    '@frontend/views' => '@frontend/themes/' . THEME_NAME,
                    '@frontend/modules' => '@frontend/themes/' . THEME_NAME . '/modules'
                ]
            ] : null,
            'class' => 'common\components\View',
            'on ' . yii\base\View::EVENT_BEGIN_PAGE => function ($event) {
                $view = $event->sender;
                // 出错时，取消第三方附加组件的加载事件
                if (is_object($view->context) && get_class($view->context) === 'yii\web\ErrorHandler') {
                    $view::offEvent();
                }
            }
        ],
        'response' => [
            'on ' . yii\web\Response::EVENT_BEFORE_SEND => function ($event) {
                $response = $event->sender;
                // 微信浏览器访问时，改变重定向的HTTP状态为200
                if (req()->getIsAjax() && common\helpers\System::isWeixin() && $response->getStatusCode() === 302) {
                    $response->setStatusCode(200);
                }
            }
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                YII_DEBUG ?
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'trace'],
                    'maxLogFiles' => 1,
                    'maxFileSize' => 1024,
                    'logFile' => '@runtime/logs/trace.log2',
                ] :
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:404'
                    ]
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/self.log',
                    'levels' => ['error', 'warning', 'info', 'trace', 'profile'],
                    'categories' => ['ChisWill']
                ]
            ]
        ],
        'assetManager' => [
            'linkAssets' => true,
            'hashCallback' => function ($path) {
                if (is_dir($path)) {
                    $dir = opendir($path);
                    $dirs = [$path];
                    while (($file = readdir($dir)) !== false) {
                        if ($file === '.' || $file === '..') {
                            continue;
                        }
                        if (is_dir($file)) {
                            $dirs[] = $path . DIRECTORY_SEPARATOR . $file;
                        }
                    }
                    closedir($dir);
                    $mtime = 0;
                    foreach ($dirs as $dir) {
                        $mtime += filemtime($dir);
                    }
                    $path .= $mtime;
                } else {
                    $path = dirname($path) . filemtime($path);
                }
                return sprintf('%x', crc32($path . Yii::getVersion()));
            },
            'forceCopy' => false,
            'appendTimestamp' => false,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        YII_ENV_PROD ? 'jquery.min.js' : 'jquery.js'
                     ],
                    'jsOptions' => [
                        'position' => yii\web\View::POS_HEAD
                    ]
                ]
            ],
            'assetMap' => [
            ]
        ]
    ]
];
