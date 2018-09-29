<?php

namespace common\components;

use Yii;
use common\helpers\Security;
use common\helpers\Inflector;
use common\helpers\FileHelper;

/**
 * Action 基类
 *
 * @author ChisWill
 */
class Action extends \yii\base\Action
{
    use \common\traits\ChisWill;

    /**
     * 调用 Ajax 请求的成功返回
     * 
     * @see common\components\WebController::success()
     */
    public static function success()
    {
        return call_user_func_array(['common\components\WebController', 'success'], func_get_args());
    }

    /**
     * 调用 Ajax 请求的失败返回
     * 
     * @see common\components\WebController::error()
     */
    public static function error()
    {
        return call_user_func_array(['common\components\WebController', 'error'], func_get_args());
    }

    /**
     * 调用 jsonp 请求的返回
     * 
     * @see common\components\WebController::jsonp()
     */
    public static function jsonp()
    {
        return call_user_func_array(['common\components\WebController', 'jsonp'], func_get_args());
    }
}
