<?php

/**
 * 快捷获取登录用户信息
 * 
 * @param  string $key     用户表的字段
 * @param  mixed  $default 获取不到时的默认值 
 * @return mixed
 */
function u($key = '', $default = null)
{
    try {
        if ($key) {
            return isset(Yii::$app->user->identity->$key) ? Yii::$app->user->identity->$key : $default;
        } else {
            return Yii::$app->user->identity ? : new frontend\components\WebUser;
        }
    } catch (\Exception $e) {
        if ($key) {
            return null;
        } else {
            return new frontend\components\WebUser;
        }
    }
}

/**
 * 快捷获取 `user` 组件信息
 */
function user()
{
    return Yii::$app->user;
}

/**
 * 快捷获取 `session` 组件信息，使用方式如下：
 * session('key')  <=>  Yii::$app->session->get('key');
 * session('key', 'value')  <=>  Yii::$app->session->set('key', 'value');
 * session('key', 'value', 3600)  <=>  Yii::$app->session->set('key', 'value', 3600);
 * session('key', null)  <=>  Yii::$app->session->remove('key');
 */
function session()
{
    switch (func_num_args()) {
        case 0:
            return Yii::$app->session;
        case 1:
            return Yii::$app->session->get(func_get_arg(0));
        case 2:
            if (func_get_arg(1) === null) {
                return Yii::$app->session->remove(func_get_arg(0));
            }
        case 3:
            call_user_func_array([Yii::$app->session, 'set'], func_get_args());
            break;
    }
}

/**
 * 快捷使用 `cookie` 助手方法，使用方式如下：
 * cookie('key')  <=>  Cookie::get('key');
 * cookie('key', null)  <=>  Cookie::remove('key');
 * cookie('key', 'value')  <=>  Cookie::set('key', 'value');
 * cookie('key', 'value', 3600)  <=>  Cookie::set('key', 'value', 3600);
 */
function cookie()
{
    switch (func_num_args()) {
        case 0:
            return Yii::$app->response->cookies;
        case 1:
            return common\helpers\Cookie::get(func_get_arg(0));
        case 2:
            if (func_get_arg(1) === null) {
                return common\helpers\Cookie::remove(func_get_arg(0));
            }
        case 3:
            call_user_func_array(['common\helpers\Cookie', 'set'], func_get_args());
            break;
    }
}

/**
 * 快捷调用 `request` 组件信息，优先获取post中的参数
 * 
 * @param  string $key     参数名
 * @param  mixed  $default get和post中都获取不到时的默认值
 * @return mixed
 */
function req($key = '', $default = null)
{
    if ($key) {
        if (Yii::$app->request->post($key) === null) {
            return Yii::$app->request->get($key, $default);
        } else {
            return Yii::$app->request->post($key);
        }
    } else {
        return Yii::$app->request;
    }
}

/**
 * 快捷post中的参数，参数不填时获取所有post参数
 * 
 * @param  string $key     参数名
 * @param  mixed  $default 获取不到时的默认值
 * @return mixed
 */
function post($key = '', $default = null)
{
    if ($key) {
        return Yii::$app->request->post($key, $default);
    } else {
        return Yii::$app->request->post();
    }
}

/**
 * 快捷get中的参数，参数不填时获取所有get参数
 * 
 * @param  string $key     参数名
 * @param  mixed  $default 获取不到时的默认值
 * @return mixed
 */
function get($key = '', $default = null)
{
    if ($key) {
        return Yii::$app->request->get($key, $default);
    } else {
        return Yii::$app->request->get();
    }
}

/**
 * 快捷调用 `response` 组件，默认使用html方式输出
 */
function res()
{
    switch (func_num_args()) {
        case 0:
            return Yii::$app->response;
        case 1:
            if (req()->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            }
            Yii::$app->response->data = func_get_arg(0);
            Yii::$app->response->send();
            return Yii::$app->response;
    }
}

/**
 * 快捷调用 `cache` 组件信息，使用方式如下：
 * cache('key')  <=>  Yii::$app->cache->get('key');
 * cache('key', 'value')  <=>  Yii::$app->cache->set('key', 'value');
 * cache('key', 'value', 3600)  <=>  Yii::$app->cache->set('key', 'value', 3600);
 * cache('key', 'value', 3600, $dependency)  <=>  Yii::$app->cache->set('key', 'value', 3600, $dependency);
 * cache('key', null)  <=>  Yii::$app->cache->delete('key');
 */
function cache()
{
    switch (func_num_args()) {
        case 0:
            return Yii::$app->cache;
        case 1:
            return Yii::$app->cache->get(func_get_arg(0));
        case 2:
            if (func_get_arg(1) === null) {
                return Yii::$app->cache->delete(func_get_arg(0));
            }
        case 3:
        case 4:
            call_user_func_array([Yii::$app->cache, 'set'], func_get_args());
            break;
    }
}

/**
 * 快捷获取 `redis` 组件信息
 */
function redis()
{
    return Yii::$app->redis;
}

/**
 * 快捷调用配置信息
 *
 * @param  string $key     配置名
 * @param  mixed  $default 配置不存在时的默认值
 * @return mixed
 */
function config($key = '', $default = null)
{
    $config = new common\components\Config;
    if ($key) {
        return $config->get($key, $default);
    } else {
        return $config;
    }
}

/**
 * 快捷支付回调地址
 *
 * @param  string $key     配置名
 * @param  mixed  $default 配置不存在时的默认值
 * @return mixed
 */
function zynotify()
{
    $data = $_GET;
    if (isset($data['returncode']) && $data['returncode'] == '00') {
        $return = [
            "memberid" => $data["memberid"], // 商户ID
            "orderid" => $data["orderid"], // 订单号
            "amount" => $data["amount"], // 交易金额
            "datetime" => $data["datetime"], // 交易时间
            "returncode" => $data["returncode"]
        ];
        ksort($return);
        reset($return);
        $string = '';
        foreach ($return as $key => $v) {
            $string .= "{$key}=>{$v}&";
        }
        $string .= "key=" . ZYPAY_KEY;
        $newSign = strtoupper(md5($string));
        if ($data['sign'] == $newSign) {
            $userCharge = common\models\UserCharge::find()->where('trade_no = :trade_no', [':trade_no' => $data['orderid']])->one();
            //有这笔订单
            if (!empty($userCharge)) {
                $tradeAmount = $data['amount'];
                if ($userCharge->charge_state == 1) {
                    $user = common\models\User::findOne($userCharge->user_id);
                    $user->account += $tradeAmount;
                    if ($user->save()) {
                        $userCharge->charge_state = 2;
                    }
                }
                $userCharge->update();
            }
            exit('ok');
        }
    }
    exit('fail');
}

/**
 * 快捷调用option表参数，使用方式如下：
 * option()  =>  获得所有系统参数
 * option('key')  =>  获得指定系统参数
 * option('key', 'value')  =>  设置指定系统参数
 */
function option($key = '', $value = null)
{
    static $options = null;
    $argsNum = func_num_args();
    if ($argsNum <= 1 && $options === null) {
        $options = common\models\Option::find()->where(['type' => common\models\Option::TYPE_SYSTEM])->asArray()->map('option_name', 'option_value');
    }
    switch ($argsNum) {
        case 0:
            return $options;
        case 1:
            return isset($options[$key]) ? unserialize($options[$key]) : null;
        case 2:
            if (!($option = common\models\Option::find()->where(['option_name' => $key, 'type' => common\models\Option::TYPE_SYSTEM])->one())) {
                $option = new common\models\Option;
                $option->option_name = $key;
                $option->type = common\models\Option::TYPE_SYSTEM;
            }
            $option->option_value = serialize($value);
            $option->save(false);
    }
}

/**
 * 快捷调用 Ajax 请求的成功返回
 * 
 * @see common\components\WebController::success()
 */
function success()
{
    return call_user_func_array(['common\components\WebController', 'success'], func_get_args());
}

/**
 * 快捷调用 Ajax 请求的失败返回
 * 
 * @see common\components\WebController::error()
 */
function error()
{
    return call_user_func_array(['common\components\WebController', 'error'], func_get_args());
}

/**
 * 快捷调用 jsonp 请求的返回
 * 
 * @see common\components\WebController::jsonp()
 */
function jsonp()
{
    return call_user_func_array(['common\components\WebController', 'jsonp'], func_get_args());
}

/**
 * 快捷调用抛出用户异常方法
 * 
 * @see common\traits\FuncTrait::throwHttpException()
 */
function throwex()
{
    call_user_func_array(['common\traits\FuncTrait', 'throwHttpException'], func_get_args());
}

/**
 * 快捷调用生成链接方法，不填参数表示直接获取当前链接
 * 
 * @see common\traits\FuncTrait::currentUrl()
 * @see common\traits\FuncTrait::createUrl()
 */
function url()
{
    if (func_num_args() === 0) {
        return call_user_func_array(['common\traits\FuncTrait', 'currentUrl'], func_get_args());
    } else {
        return call_user_func_array(['common\traits\FuncTrait', 'createUrl'], func_get_args());
    }
}

/**
 * 快捷记录信息到文件
 * 
 * @see common\traits\FuncTrait::log()
 */
function l()
{
    call_user_func_array(['common\traits\FuncTrait', 'log'], func_get_args());
}

//返回错误
function jsonError($message = '', $url = null)
{
    $return['msg'] = $message;
    $return['data'] = '';
    $return['code'] = -1;
    $return['url'] = $url;
    return json_encode($return);
}

//返回正确
function jsonSuccess($message = '', $data = '', $url = null)
{
    $return['msg'] = $message;
    $return['data'] = $data;
    $return['code'] = 1;
    $return['url'] = $url;
    return json_encode($return);
}

// 写入日志
function setLog($logArray)
{
    $dirOut = './runtime/logs';
    if (!is_dir($dirOut)) {
        mkdir($dirOut, 0777);
    }
    $dirInt = $dirOut . '/' . date('Y-m');
    if (!is_dir($dirInt)) {
        mkdir($dirInt, 0777);
    }
    $logcontent = '********************************************    ' . date('Y-m-d H:i:s') . "\r\n";
    foreach ($logArray as $k => $v) {
        $logcontent .= $k . ':' . $v . "\r\n";
    }
    $res = fopen($dirInt . '/' . date('d') . '.txt', 'a+');
    fwrite($res, $logcontent, strlen($logcontent));
    fclose($res);
}
