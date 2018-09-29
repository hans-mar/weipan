<?php

namespace common\components;

use Yii;
use yii\base\UnknownPropertyException;

/**
 * 事件的基类，封装了自由属性的特性
 *
 * @author ChisWill
 */
class Event extends \yii\base\Event
{
    private static $_params = [];

    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            if (array_key_exists($name, self::$_params)) {
                return self::$_params[$name];
            } else {
                return '';
            }
        }
    }

    public function __set($name, $value)
    {
        try {
            parent::__set($name, $value);
        } catch (UnknownPropertyException $e) {
            self::$_params[$name] = $value;
        }
    }
}
