<?php

namespace console\models;

use Yii;
use common\models\Order;
use common\models\Product;
use common\helpers\StringHelper;
use common\helpers\Curl;

class GatherYiyuan extends Gather
{
    public $url = 'http://route.showapi.com/';
    // 产品列表
    public $productList = [
        'sh000001' => [
           'name' => '上证指数',
           'param' => '131-45',
        ]
    ];

    public function run()
    {
        $this->switchMap = option('risk_product') ?: [];
        foreach ($this->productList as $tableName => $info) {
            $showapi_appid = '27893';  //替换此值,在官网的"我的应用"中找到相关值
            $showapi_secret = '5252f15a7c144820a4eab89e7b4997d5';  //替换此值,在官网的"我的应用"中找到相关值
            $paramArr = [
                 'showapi_appid' => $showapi_appid
                 //添加其他参数
            ];
            //创建参数(包括签名的处理)
            $createParam = function ($paramArr, $showapi_secret) {
                 $paraStr = "";
                 $signStr = "";
                 ksort($paramArr);
                 foreach ($paramArr as $key => $val) {
                     if ($key != '' && $val != '') {
                        $signStr .= $key . $val;
                        $paraStr .= $key . '=' . urlencode($val) . '&';
                     }
                 }
                 $signStr .= $showapi_secret;//排好序的参数加上secret,进行md5
                 $sign = strtolower(md5($signStr));
                 $paraStr .= 'showapi_sign=' . $sign;//将md5后的值作为参数,便于服务器的效验
                 return $paraStr;
            };
             
            $param = $createParam($paramArr, $showapi_secret);
            $url = $this->url . $info['param'] . '?' . $param;
            // 每个品类，先采集最新价格
            try {
                $data = json_decode($this->getHtml($url), true)['showapi_res_body']['indexList'];
                foreach ($data as $row) {
                    if ($row['code'] == $tableName) {
                        $info = [
                            'price' => sprintf('%.2f', $row['nowPrice']),
                            'open' => sprintf('%.2f', $row['todayOpenPrice']),
                            'high' => sprintf('%.2f', $row['maxPrice']),
                            'low' => sprintf('%.2f', $row['minPrice']),
                            'close' => sprintf('%.2f', $row['yestodayClosePrice']),
                            'diff' => sprintf('%.2f', $row['diff_money']),
                            'diff_rate' => sprintf('%.2f', $row['diff_rate']),
                            'time' => date('Y-m-d H:i:s', strtotime($row['time']))
                        ];
                        $this->insert($tableName, $info);
                    }
                }
            } catch (\Exception $e) {
                // do nothing...
            }
        }

        // 监听是否有人应该平仓
        $this->listen();
    }
}
