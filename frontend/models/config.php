<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 17/5/8
 * Time: 下午2:04
 */

$MERID="100519189";
$KEY="j6t7udQuxEi5";
$TRANS_URL="http://geeepay.com:8080";
#$TRANS_URL="http://localhost:8081";
$SIGN_TYPE="MD5";


function signString($input){
    $pieces = explode("&", $input);
    sort($pieces);
    global $KEY;
    $string='';
    foreach ($pieces as $value){
        if($value!=''){
            $vlaue1= explode("=", $value);
            if($vlaue1[1]!=''&&$value[1]!=null){
                $string=$string.$value.'&';
            }
        }
    }
    $string=$string.'key='. $KEY;
    $sign=strtoupper(md5($string));
    $string=$string.'&signData='.$sign;
    return $string;
}

function sign($input,$key){
    $pieces = explode("&", $input);
    sort($pieces);

    global $KEY;
    $string='';
    foreach ($pieces as $value){
        if($value!=''){
            $vlaue1= explode("=", $value);
            if($vlaue1[1]!=''&&$value[1]!=null){
                $string=$string.$value.'&';
            }
        }
    }
    $string=$string.'key='. $key;
    file_put_contents('sign.txt',$string,FILE_APPEND);
    //return $string;
    $sign=strtoupper(md5($string));
    return $sign;
}