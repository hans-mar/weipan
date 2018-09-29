<?php

namespace common\helpers;

class Url extends \yii\helpers\BaseUrl
{
    /**
     * url安全的base64编码
     * 
     * @param  string $string
     * @return string
     */
    public static function base64encode($string)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }

    /**
     * url安全的base64解码
     * 
     * @param  string $string
     * @return string
     */
    public static function base64decode($string)
    {
        $string = str_replace(['-', '_'], ['+', '/'], $string);
        $mod4 = strlen($string) % 4;
        if ($mod4) {
            $string .= substr('====', $mod4);
        }
        return base64_decode($string);
    }
}
