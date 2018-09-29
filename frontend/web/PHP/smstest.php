<?php
$statusStr = array(
		"0" => "短信发送成功",
		"-1" => "参数不全",
		"-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
		"30" => "密码错误",
		"40" => "账号不存在",
		"41" => "余额不足",
		"42" => "帐户已过期",
		"43" => "IP地址限制",
		"50" => "内容含有敏感词"
	);	
$smsapi = "http://www.smsbao.com/"; //短信网关
$user = "zhipin"; //短信平台帐号
$pass = md5("b3cf2d05e90983b262207655dd65dce7"); //短信平台密码
$content="【智聘网络】您好，您的验证码是1212";//要发送的短信内容
$phone = "17507624888";
$sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
$result =file_get_contents($sendurl) ;
echo $statusStr[$result];
?>