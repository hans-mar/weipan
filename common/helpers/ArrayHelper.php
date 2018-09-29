<?php

namespace common\helpers;

class ArrayHelper extends \yii\helpers\BaseArrayHelper
{
    /**
     * 整理自定义的配置数组成统一格式， 以下是例子:
     * ```php
     * $options = [
     *     'id',
     *     'id' => ['header' => 'name'],
     *     'name' => 'email',
     *     ['type' => 'text']
     * ];
     *
     * $result = ArrayHelper::resetOptions($options, ['key' => 'field', 'value' => 'title']);
     * // the result is:
     * // [
     * //      ['field' => 'id'],
     * //      'id' => ['header' => 'name', 'field' => 'id'],
     * //      'name' => ['field' => 'name', 'title' => 'email'],
     * //      ['type' => 'text']
     * // ]
     * ```
     * 
     * @param array          $options 配置数组 
     * @param array          $config  设置各种情况配置项的键名
     * @return array                  整理后的配置数组
     */
    public static function resetOptions(array $options, $config = ['key' => 'field', 'value' => 'title', 'callback' => 'callback'])
    {
        $newOptions = [];
        $config['callback'] = self::getValue($config, 'callback', 'callback');
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $option = $value;
            } else {
                $option = [];
            }
            
            if (is_string($key)) {
                $option[$config['key']] = $key;
                if (is_callable($value) && !is_string($value)) {
                    $option[$config['callback']] = $value;
                } elseif (!is_array($value)) {
                    $option[$config['value']] = $value;
                }
            } else {
                if (is_callable($value) && !is_string($value)) {
                    $option[$config['callback']] = $value;
                } elseif (is_string($value)) {
                    $option[$config['key']] = $value;
                }
            }
            
            $newOptions[$key] = $option;
        }

        return $newOptions;
    }


    /**
     * 转置数组，input框name属性设置为 `name="name[]"`时，提交后POST数组为 
     * ```php
     * $array = [
     *   'name' => ['Peter', 'Linda'],
     *   'email' => ['a@a.a', 'b@b.b']
     * ];
     * 
     * $res = ArrayHelper::transform($array);
     * 
     * result:
     * [
     *   ['name' => 'Peter', 'email' => 'a@a.a'],
     *   ['name' => 'Linda', 'email' => 'b@b.b']
     * ]
     * ```
     * 另外，再次调用可以转换回来
     * 
     * @param  array $array 需要被转置在数组
     * @return array        转换后在数组
     */
    public static function transform($array)
    {
        if (empty($array)) {
            return $array;
        }
        
        $ret = [];

        if (is_string(key($array))) {
            $length = 0;
            array_walk($array, function ($arr) use (&$length) {
                $count = count($arr);
                if ($count > $length) {
                    $length = $count;
                }
            });
            for ($i = 0; $i < $length; $i++) {
                $item = [];
                foreach ($array as $key => $value) {
                    $item[$key] = self::getValue($value, $i, '');
                }
                $ret[] = $item;
            }
        } else {
            foreach ($array as $item) {
                foreach ($item as $key => $value) {
                    $ret[$key][] = $value;
                }
            }
        }
        
        return $ret;
    }

    /**
     * 比较两个数组，返回他们的差集
     * 与 array_diff 不同的是，返回两个数组：
     * 第一个数组是 $array1 没有，但 $array2 有的元素
     * 第二个数组是 $array1 有，但 $array2 没有的元素
     * ```php
     * $arr1 = ['apple', 'orange'];
     * $arr2 = ['banana', 'apple'];
     * list($addArr, $removeArr) = ArrayHelper::diff($arr1, $arr2);
     * ```
     * 
     * result:
     * $addArr = ['banana'];
     * $removeArr = ['orange'];
     * 
     * @param  array $array1 第一个数组，比较结果是以该数组为基础
     * @param  array $array2 第二个数组，用来比较的参照数组
     * @return array
     */
    public static function diff(array $array1, array $array2)
    {
        $result1 = $result2 = [];

        foreach ($array2 as $value) {
            if (!in_array($value, $array1)) {
                $result1[] = $value;
            }
        }

        foreach ($array1 as $value) {
            if (!in_array($value, $array2)) {
                $result2[] = $value;
            }
        }

        return [$result1, $result2];
    }

    /**
     * 二维数组的过滤, 每个条件之间都是用AND来连接
     * ```php
     * $data = User::findAll();
     * $data = ArrayHelper::filter($data, ['eq' => ['name' => 'ChisWill'], 'lt' => ['age' => 18]]);
     * ```
     * 
     * @param  array  $array      待过滤的二维数组
     * @param  array  $conditions 条件数组，参数形式参考说明中的例子
     * @return array
     */
    public static function filter($array, $conditions = [])
    {
        if (empty($conditions)) {
            return $array;
        }
        // 定义符号
        $getSymbol = function ($symbol) {
            if (in_array($symbol, ['==', '===', '!=', '!==', '>', '<', '>=', '<='])) {
                return $symbol;
            }
            switch ($symbol) {
                case 'eq':
                    $s = '==';
                    break;
                case 'ne':
                    $s = '!=';
                    break;
                case 'gt':
                    $s = '>';
                    break;
                case 'lt':
                    $s = '<';
                    break;
                case 'get':
                    $s = '>=';
                    break;
                case 'let':
                    $s = '<=';
                    break;
                case 'in':
                    $s = 'in_array';
                    break;
                case 'nin':
                    $s = '!in_array';
                    break;
                default:
                    $s = '==';
                    break;
            }
            return $s;
        };

        $condition = '';
        foreach ($conditions as $symbol => $conditionItems) {
            foreach ($conditionItems as $field => $value) {
                if ($symbol === 'in' || $symbol === 'nin') {
                    $condition .= $getSymbol($symbol) . '($array[\'' . $field . '\'],' . var_export($value, true) . ')';
                } else {
                    $condition .= '$array[\'' . $field . '\']' . $getSymbol($symbol) . '\'' . $value . '\'';
                }
                $condition .= '&&';
            }
        }
        $condition = trim($condition, '&&'); 
        
        return array_filter($array, function ($array) use ($condition) {
            eval('$res = ' . $condition . ';');
            return !!$res;
        });
    }

    /**
     * 打乱数组并保留键值
     * 
     * @param  array $array
     * @return array
     */
    public static function shuffle(array $array)
    {
        $keys = array_keys($array);
        shuffle($keys);
        $ret = [];
        foreach ($keys as $key) {
            $ret[$key] = $array[$key];
        }
        return $ret;
    }

    /**
     * 返回一个随机的键值
     * 
     * @param  array $array 任意数组
     * @param  int   $num   返回的元素个数
     * @return array
     */
    public static function random(array $array, $num = 1)
    {
        $max = count($array) - 1;
        for ($i = 0; $i < $num; $i++) {
            $randomKeys[] = mt_rand(0, $max);
        }
        return implode('', array_map(function ($key) use ($array) {
            return $array[$key];
        }, $randomKeys));
    }    
}
