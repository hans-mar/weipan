<?php

namespace common\helpers;

class StringHelper extends \yii\helpers\BaseStringHelper
{
    /**
     * 在 yii 助手方法的基础上，调整了参数位置，并默认忽略了空值
     *
     * @see yii\helpers\BaseStringHelper::explode()
     */
    public static function explode($delimiter = ',', $string = '', $trim = true, $skipEmpty = true)
    {
        return parent::explode($string, $delimiter, $trim, $skipEmpty);
    }

    /**
     * 高亮字符串
     * 
     * @param  string $string  要高亮的原字符串
     * @param  string $replace 要被高亮的部分
     * @param  string $prefix  HTML开始标签
     * @param  string $postfix HTML结束标签
     * @return string          高亮后的字符串
     */
    public static function highlight($string, $replace, $prefix = '<font color="red">', $postfix = '</font>')
    {
        if (empty($string) || empty($replace)) {
            return $string; //如果有一个为空 直接返回字符串
        }
        // 得到字符串长度
        $strlength = strlen($string); // 字符串的长度
        $prelength = strlen($prefix); // 要添加 前缀的长度
        $postlength = strlen($postfix); //要添加 后缀的长度
        $rellength = strlen($replace); //要替换字符串的长度
        $relstrlower = strtolower($replace); // 将要替换的字符串转化为小写
        // 判断 替换的长度大于字符串长度，直接返回原来的字符串
        if ($rellength > $strlength) {
            return $string;
        }
        // 开始遍历
        for ($i = 0; $i <= $strlength - $rellength; ++$i) {
            $substr = substr($string, $i, $rellength);
            $substrlower = strtolower($substr);//也转换为小写来判断
            // 判断是否相等
            if ($substrlower == $relstrlower) {
                // 开始拼凑字符串
                $string = substr($string, 0, $i) . $prefix . $substr . $postfix . substr($string, $i + $rellength, $strlength);
                //重新 矫正 字符串长度
                $strlength += $prelength + $postlength;//重新计算字符串总长度
                $i += $rellength - 1 + $prelength + $postlength;// 改变 $i 变量的值
            }
        }

        return  $string;
    }

    /**
     * 生成指定位数的随机码
     * 
     * @param  integer $length 随机码的长度
     * @param  string  $type   随机码的类型
     * @return string          生成后的随机码
     */
    public static function random($length = 8, $type = 'all')
    {
        $nums = '0123456789';
        $alps = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $oths = '!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        $chars = '';
        switch ($type) {
            case 'n':
                $chars = $nums;
                break;
            case 'a':
                $chars = $alps;
                break;
            case 'o':
                $chars = $oths;
                break;
            case 'w':
                $chars = $alps .= $nums;
                break;
            default:
                $chars = $alps .= $nums .= $oths;
                break;
        }
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * 中英文都按一个字符算的字符串长度截取，并在末尾可增加省略号
     * 
     * @param  string  $string 待截取的字符串
     * @param  integer $length 截取的长度
     * @param  string  $etc    截取后末尾的符号，默认为省略号
     * @return string
     */
    public static function subContent($string, $length, $etc = '...')
    {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($etc !== false && $i < $strlen) {
            $result .= $etc;
        }
        return $result;
    }

    /**
     * 富文本的字符串长度截取，并在末尾可增加省略号
     * 
     * @param  string  $string 富文本
     * @param  integer $length 截取的长度
     * @param  string  $etc    截取后末尾的符号，默认为省略号
     * @return string
     */
    public static function subHtml($string, $length, $etc = '...')
    {
        $result = '';
        $strlen = strlen($string);
        $start = false;
        $lastTag = '';
        $addSuffix = false;
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $char = substr($string, $i, 1);
                if ($char === '>') {
                    $start = false;
                    $result .= $char;
                    $lastTag .= $char;
                    continue;
                }
                if ($char === '<') {
                    $lastTag = '';
                    $start = true;                
                }
                if ($start === true) {
                    $lastTag .= $char;
                    $result .= $char;
                    continue;
                }
                $result .= $char;
                $length -= 0.5;
            }
            if (($i < $strlen) && ($length > 0)) {
                if ($lastTag) {
                    $addSuffix = true;
                }
            }        
        }
            
        if ($etc !== false && $i < $strlen) {
            $result .= $etc;
        }
        if ($addSuffix == true) {
            $result .= str_replace('<', '</', $lastTag);
        }
        return $result;
    }
}
