<?php

namespace common\helpers;

use yii\base\InvalidParamException;

class Date
{
    /**
     * 将任意一个给定的过去的时间，转换成过去了多久的形式
     * 
     * @param  string|integer $time 时间戳或是`Y-m-d H:i:s`格式的字符串
     * @return string               时间经过多久的文本描述
     */
    public static function age($time)
    {
        if (is_string($time)) {
            $time = strtotime($time);
            if ($time === false) {
                throw new InvalidParamException('传入的参数："' . $time . '"有误，不是时间格式的字符串~！');
            }
        }
        $diff = time() - $time;
        if ($diff <= 0) {
            return '刚刚';
        }
        $map = [
            31536000 => '年',
            2592000 => '个月',
            604800 => '星期',
            86400 => '天',
            3600 => '小时',
            60 => '分钟',
            1 => '秒'
        ];
        foreach ($map as $s => $v) {
            if (0 != $c = floor($diff / $s)) {
                return $c . $v . '前';
            }
        }
    }
}
