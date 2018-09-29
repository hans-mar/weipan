<?php

namespace common\helpers;

use Yii;
use \common\models\User;
use \common\helpers\Cookie;
class Cookie
{
    /**
     * 设置 COOKIE
     * 
     * @param string  $name   名称
     * @param mixed   $value  值
     * @param integer $expire 过期时间，默认当前会话
     */
    public static function set($name, $value, $expire = 0)
    {
        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => $name,
            'value' => $value,
            'expire' => $expire
        ]));
    }

    /**
     * 获取 COOKIE
     * 
     * @param  string $name         名称
     * @param  mixed  $defaultValue 默认值
     * @return mixed
     */
    public static function get($name, $defaultValue = null)
    {
        return Yii::$app->request->cookies->getValue($name, $defaultValue);
    }

    /**
     * 删除 COOKIE
     * 
     * @param  string $name 名称
     */
    public static function remove($name)
    {
        Yii::$app->request->cookies->remove($name);
    }

    /**
     * 检查 COOKIE
     * 
     * @param  string  $name 名称
     * @return boolean
     */
    public static function has($name)
    {
        return Yii::$app->request->cookies->has($name);
    }
}
