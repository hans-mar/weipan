<?php

namespace common\components;

use Yii;

/**
 * 配置参数的封装，将数据库与文件中的配置参数整合在一起，使用统一的方式调用
 * 当配置名相同时，优先使用数据库中的参数值
 * 使用方式如下：
 * ```php
 * $users = config('web_name') ?: [];
 * // 或者
 * $users = config()->get('web_name', []);
 * ```
 * 
 * @author ChisWill
 */
class Config implements \ArrayAccess
{
    private $_config = [];

    public function __construct()
    {
        $this->_config = array_merge(Yii::$app->params, \common\modules\setting\models\Setting::getConfig());
    }

    /**
     * 判断指定名称的配置项是否存在
     * 
     * @param  string  $name 配置名
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->_config[$name]);
    }

    /**
     * 获取指定名称的配置项，如果不存在或没设置，则返回默认值
     * 
     * @param  string $name         配置名
     * @param  mixed  $defaultValue 默认值
     * @return mixed
     */
    public function get($name = null, $defaultValue = null)
    {
        if ($name === null) {
            return $this->_config;
        } else {
            return $this->has($name) ? $this->_config[$name] : $defaultValue;
        }
    }

    /**
     * 暂时设定某配置的值
     * 
     * @param string $name  配置名
     * @param mixed  $value 要设置的值
     */
    public function set($name, $value)
    {
        $this->_config[$name] = $value;
    }

    /**
     * Returns whether there is an element at the specified offset.
     * This method is required by the SPL interface [[\ArrayAccess]].
     * It is implicitly called when you use something like `isset($config[$offset])`.
     * @param mixed $offset the offset to check on
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Returns the element at the specified offset.
     * This method is required by the SPL interface [[\ArrayAccess]].
     * It is implicitly called when you use something like `$value = $config[$offset];`.
     * @param mixed $offset the offset to retrieve element.
     * @return mixed the element at the offset, null if no element is found at the offset
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Sets the element at the specified offset.
     * This method is required by the SPL interface [[\ArrayAccess]].
     * It is implicitly called when you use something like `$config[$offset] = $item;`.
     * @param integer $offset the offset to set element
     * @param mixed $item the element value
     */
    public function offsetSet($offset, $item)
    {
        $this->set($offset, $item);
    }

    /**
     * Sets the element value at the specified offset to null.
     * This method is required by the SPL interface [[\ArrayAccess]].
     * It is implicitly called when you use something like `unset($config[$offset])`.
     * @param mixed $offset the offset to unset element
     */
    public function offsetUnset($offset)
    {
        $this->_config[$offset] = null;
    }
}
