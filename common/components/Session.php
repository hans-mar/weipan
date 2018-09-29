<?php

namespace common\components;

/**
 * 定制化的`session`组件，定制内容如下：
 * - 增加了 `__get()`、`__set()` 访问形式
 * - `set()` 方法增加过期时间（单位秒）
 * - 三种 get 访问方式增加了对过期时间的判断来获取值，如果过期则返回 `null`
 *
 * Yii提供以下session类实现不同的session存储方式：
 * - yii\web\Session 默认，存储session数据为文件到服务器上，
 * - yii\web\DbSession: 存储session数据在数据表中
 * - yii\web\CacheSession: 存储session数据到缓存中，缓存和配置中的缓存组件相关
 * - yii\redis\Session: 存储session数据到以redis 作为存储媒介中
 * - yii\mongodb\Session: 存储session数据到MongoDB.
 *
 * 所有这些session类支持相同的API方法集，因此，切换到不同的session存储介质不需要修改项目使用session的代码.
 * 
 * 通过修改继承的类来选择不同的session存储介质.
 *
 * @author ChisWill
 */
class Session extends \yii\web\Session
{
    public function __get($name)
    {
        $this->open();

        return isset($_SESSION[$name]) ? $this->getSessionValue($_SESSION[$name]) : null;
    }

    public function __set($name, $value)
    {
        $this->open();

        $_SESSION[$name] = $value;
    }

    public function __isset($name)
    {
        $this->open();

        return isset($_SESSION[$name]);
    }

    public function __unset($name)
    {
        $this->open();

        unset($_SESSION[$name]);
    }

    /**
     * @see yii\web\Session::get()
     */
    public function get($key, $defaultValue = null)
    {
        return $this->getSessionValue(parent::get($key, $defaultValue));
    }

    /**
     * @see yii\web\Session::set()
     * @param $expire integer set session expire time(unit is second).
     */
    public function set($key, $value, $expire = 0)
    {
        if ($expire > 0) {
            $value = [
                'value' => $value,
                '_expire' => $expire + time()
            ];
        }

        parent::set($key, $value);
    }

    /**
     * @see yii\web\Session::offsetGet()
     */
    public function offsetGet($offset)
    {
        return $this->getSessionValue(parent::offsetGet($offset));
    }

    /**
     * Get the value according to the expiration date.
     *
     * @param  mixed $value session variable value.
     * @return mixed
     */
    private function getSessionValue($value)
    {
        if (is_array($value) && isset($value['_expire'])) {
            if ($value['_expire'] >= time()) {
                return $value['value'];
            } else {
                return null;
            }
        } else {
            return $value;
        }
    }
}
