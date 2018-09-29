<?php

namespace common\helpers;

class Third
{
    /**
     * 获取美元汇率
     * 
     * @param  float $rate 默认汇率，必须填写，接口有时可能会获取不到
     * @return float
     */
    public static function getUsdRate($rate)
    {
        $contents = @file_get_contents('http://api.k780.com:88/?app=finance.rate&scur=USD&tcur=CNY&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=json');
        if ($contents) {
            $res = json_decode($contents);
            if ($res->success) {
                $rate = $res->result->rate;
            }
        }
        return $rate;
    }

    /**
     * 发送短信
     * 
     * @param  integer $mobile  目标手机号
     * @param  string  $content 发送信息
     * @return boolean
     */
    public static function sendsms($mobile, $content)
    {
        $suffix = '';
        $content = $content . $suffix;
        $content = mb_convert_encoding($content, 'gb2312', 'utf-8');
        $flag = 0;
        //要post的数据
        $argv = [
            'sn' => '',
            'pwd' => '',
            'mobile' => $mobile,
            'content' => $content
        ];
        //构造要post的字符串
        $params = '';
        foreach ($argv as $key=>$value) {
            if ($flag != 0) {
                $params .= "&";
                $flag = 1;
            }
            $params .= $key . "="; 
            $params .= urlencode($value);
            $flag = 1;
        }
        $length = strlen($params);
        //创建socket连接
        $fp = @fsockopen("sdk2.entinfo.cn", 80, $errno, $errstr, 10) or exit($errstr . "--->" . $errno);
        //构造post请求的头
        $header = "POST /z_send.aspx HTTP/1.1\r\n";
        $header .= "Host:sdk2.entinfo.cn\r\n";
        $header .= "Referer:/mobile/sendpost.php\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . $length . "\r\n";
        $header .= "Connection: Close\r\n\r\n";
        //添加post的字符串
        $header .= $params . "\r\n";
        //发送post的数据
        fputs($fp, $header);
        $inheader = 1;
        while (!feof($fp)) {
            $ret = fgets($fp, 1024); //去除请求包的头只显示页面的返回数据
            if ($inheader && ($ret == "\n" || $ret == "\r\n")) {
                $inheader = 0;
            }
        }
        fclose($fp);
        if ($ret == 1) {
            return true;
        } else {
            //echo '短信发送失败,请根据返回值查看相关错误问题 返回值'.$ret ;
            return $ret;
        }
    }
}