<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/1 0001
 * Time: 上午 12:55
 */
$str = '{"high": "7231.73", "last": "7018.90", "timestamp": "1522522810", "bid": "7025.37", "vwap": "6998.82", "volume": "12043.67713900", "low": "6729.28", "ask": "7029.90", "open": "6839.63"}';
//$str = 'var hq_str_btc_btcbitstamp="16:40:12,6910.7000,6915.5000,7200.0000,480,7200.0000,7189.8000,6591.8000,6913.0000,比特币兑美元(Bitstamp报价),16981.0000,2018-03-31";';
$info = 'btc_btcbitstamp';

if (in_array($info, array('btc_btcbitstamp', 'btc_btcbitstamp', 'btc_btcbitstamp', 'btc_btcbitstamp'))) {
    $resultarr = explode(',', $str);
    $price = $resultarr[3];

    $arr = array();
    for ($i = 0; $i < count($resultarr); $i++) {
        $arr[$i] = trim(str_replace('"', '', explode(':', $resultarr[$i])[1]));
    }

    $diff = $price - $arr[3];
    if ($diff == 0) {
        $diff_rate = 0.00;
    } else {
        $diff_rate = number_format($diff / $arr[3] * 100, 2, ".", "");
    }
    // echo $resultarr[sizeof($resultarr) - 2] ." " .explode('"', $resultarr[sizeof($resultarr) - 1])[0];
//    $dtime = strtotime(explode('"', $arr[sizeof($arr) - 1])[0] . " " . explode('"', $arr[0])[1]);
    // echo date('Y-m-d H:i:s', $dtime);
    $data = [
        'price' => $price,
        'open' => $arr[8],//
        'high' => $arr[0],//
        'low' => $arr[6],//
        'close' => $arr[3],
        'diff' => $diff,
        'diff_rate' => $diff_rate,
        'time' => date('Y-m-d H:i:s', $arr[2])//
    ];
}