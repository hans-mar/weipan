<?php
/**
 * 公共常量定义
 */
const PAGE_SIZE = 10;
const THEME_NAME = 'basic';
const SECRET_KEY = 'ChisWill';

const WEB_DOMAIN = 'http://ana00.cn';

const WX_APPID = 'wxe46d9ad1b506e177';
const WX_MCHID = '1337714901';
const WX_KEY = 'VKcJg2LUnnRPjmYtPX3Tfm8vqradppF9';
const WX_APPSECRET = '2a56e74df034c69836228d9f442fcd13';
const WX_TOKEN = 'aa95f86975770f56a4186ad58c4e3be5';

const HX_ID = '193439';
const HX_TID = '1934390012';
const HX_MERCERT = 'PODXWNx5N2HsgSTM6xOe9F7V5B3g04aC8gMPYfaTlzA0m0NZWoo0fgDczc0oYjFq6hhcabqxEoJesUcNnKTQxUD0QfXWlrRUuCQaDK9aqjxPFptsREBhk5eSv5N7vLTM';

const HX_PAY_DOMAIN = 'http://pay.mantingfen.cn';

//中云支付
const ZYPAY_ID = '12351';
const ZYPAY_KEY = 'IPwWl4zYS5d38ZRx1mZzM6wq7RTscO';

const ATTR_CREATED_AT = 'created_at';
const ATTR_CREATED_BY = 'created_by';
const ATTR_UPDATED_AT = 'updated_at';
const ATTR_UPDATED_BY = 'updated_by';
// 云托付
const EXCHANGE_ID = '2010';
const EXCHANGE_MDKEY = '70afbbdd0ae744d0b0e3ddd81e025ae6';
//秒充宝
const MCB_KEY ='KISYTRKJShy';
const MCB_MD5KEY='SDYFGWERUH';
const MCB_APPID=2018032064;
const MCB_APPKEY='2d7b71187fbcb38fb7b11c4b1aa00c96';
const MCB_PAY_URL='http://pay.cplll.cn/pay/pay.php';
const MCB_URL_NO='/pay/mcbnotify';
const MCB_URL_RE='/pay/mcbreturn';
//yfzf
const YF_KEY ='KISYTRKJShy';
const YF_URL_NO='http://www.shzb118.com/pay/yfnotify';
const YF_URL_RE='http://www.shzb118.com/user/index';


//鲨鱼支付

const SY_ID = '868403';
const SY_KEY = '6309d525A9C44c73a9DCE95dF760500d';
const SY_PAY_URL = 'http://47.96.2.119:7001/request-pay';
const SY_URL_NO = 'http://www.shzb118.com/pay/synotify';
const SY_URL_RE= 'http://www.shzb118.com/pay/syreturn';


/**
 * 路径别名定义
 */
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('api', dirname(dirname(__DIR__)) . '/api');
/**
 * 引入自定义函数
 */
$files = common\helpers\FileHelper::findFiles(Yii::getAlias('@common/functions'), ['only' => ['suffix' => '*.php']]);
array_walk($files, function ($file) {
    require $file;
});
/**
 * 公共变量定义
 */
common\traits\ChisWill::$date = date('Y-m-d');
common\traits\ChisWill::$time = date('Y-m-d H:i:s');
/**
 * 绑定验证前事件，为每个使用`file`验证规则的字段自动绑定上传组件
 */
common\components\Event::on('common\components\ARModel', common\components\ARModel::EVENT_BEFORE_VALIDATE, function ($event) {
    foreach ($event->sender->rules() as $rule) {
        if ($rule[1] === 'file') {
            $fieldArr = (array) $rule[0];
            foreach ($fieldArr as $field) {
                $event->sender->setUploadedFile($field);
            }
        }
    }
});
/**
 * 日志组件的全局默认配置
 */
Yii::$container->set('yii\log\FileTarget', [
    'logVars' => [],
    'maxLogFiles' => 5,
    'maxFileSize' => 1024 * 5,
    'prefix' => ['common\models\Log', 'formatPrefix']
]);
Yii::$container->set('yii\log\DbTarget', [
    'logVars' => [],
    'prefix' => ['common\models\Log', 'formatPrefix']
]);

        
        