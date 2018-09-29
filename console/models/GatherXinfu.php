<?php

namespace console\models;

use Yii;
use common\models\Order;
use common\models\Product;
use common\models\DataAll;
use common\models\ProductParam;
use common\helpers\StringHelper;
use common\helpers\Curl;

class GatherXinfu extends Gather
{
    // 产品列表
    public $productList = [
        'ag' => [
           'name' => '现货白银',
           'url' => 'http://www.xftz.cn/info/ygy_js.php?callback=jQuery183040664484569544246_1472732114837&_=1472732114849',
           'typeprefix' => ''
        ],
        // 'oil' => [
        //     'name' => '粤国际油',
        //     'url' => 'http://www.xftz.cn/info/ygjyin_js.php?ygjType=OIL&callback=jQuery18309495391003487477_1472908107006&_=1472908107159',
        //     'typeprefix' => 'OIL'
        // ],
        // 'xhn' => [
        //     'name' => '现货镍',
        //     'url' => 'http://www.xftz.cn/info/ni_js.php?callback=jQuery18309952453525257471_1480414405833&_=1472732114849',
        //     'typeprefix' => 'XHN'
        // ],
        // 'conc' => [
        //     'name' => '原油',
        //     'url' => 'http://www.xftz.cn/info/yyzs_js.php?callback=jQuery183003229861600770123_1481679924274&_=1481683365018',
        //     'typeprefix' => 'CONC'
        // ],
        // 'cu0' => [
        //     'name' => '粤国际铜',
        //     'url' => 'http://www.xftz.cn/info/ygjyin_js.php?ygjType=CU&callback=jQuery1830618806125568697_1472908852515&_=1472908852674',
        //     'typeprefix' => 'CU'
        // ],
        // 'xau' => [
        //     'name' => '黄金',
        //     'url' => 'http://www.xftz.cn/info/ldj_js.php?callback=jQuery1830015559766455635393_1481592345800&_=1481592366008',
        //     'typeprefix' => 'XAU'
        // ],
        // 'gdpt' => [
        //    'name' => '粤贵铂',
        //    'url' => 'http://www.xftz.cn/info/ygbo_js.php?callback=jQuery183017865482741409067_1472906610734&_=1472906610857',
        //    'typeprefix' => ''
        // ],
        // 'usd' => [
        //     'name' => '美元指数',
        //     'url' => 'http://www.xftz.cn/info/myzs_js.php?callback=jQuery1830024947216159511676_1472909362613&_=1472909362796',
        //     'typeprefix' => ''
        // ],
    ];
    // 间隔时间(s) => 参数名称
    public $typeList = [
        300 => '5_fen',
        600 => '10_fen',
        1800 => '30_fen',
        3600 => '60_fen',
        60 => 'fen',
        86400 => 'day'
    ];

    public function run()
    {
        $this->switchMap = option('risk_product') ?: [];
        // 自身产品
        $products = Product::find()->where(['state' => 1, 'on_sale' => 1, 'source' => 2])->select('table_name, trade_time, id')->asArray()->all();
        $this->productList = array_merge($this->productList, $products);
        foreach ($this->productList as $tableName => $info) {
            if (is_int($tableName)) {
                // if ((date('w') == 0 && date('G') > 5) || (date('G') < 5 && date('w') == 1)) {
                //     return false;
                // }
                $start = strtotime(date('Y-m-d 00:00:00', time()));
                if ($info['trade_time']) {
                    $timeArr = unserialize($info['trade_time']);
                    $start = strtotime(date('Y-m-d ' . $timeArr[0]['start'] . ':00'));
                    $time = end($timeArr);
                    $end = strtotime(date('Y-m-d ' . $time['end'] . ':00'));
                    if ($start > $end) {
                        if ($start > time() && $end < time()) {
                            continue;
                        } 
                    } else {
                        if ($start > time() || $end < time()) {
                            continue;
                        } 
                    }
                }
                if (empty($param = session('initDataParam' . $info['table_name']))) {
                    $productParam = ProductParam::findOne($info['id']);
                    $param = $productParam->attributes;
                    session('initDataParam' . $info['table_name'], $productParam->attributes, 1800);
                }

                if (empty($price = session('initData' . $info['table_name']))) {
                    $dataAll = DataAll::findOne($info['table_name']);
                    $price = $dataAll->price;
                }
                $price += mt_rand($param['start_point'], $param['end_point']);
                session('initData' . $info['table_name'], $price);

                //插入开盘价和昨日收盘价
                $nowTime = date('Y-m-d 09:00:00', time());
                $insertOpen = strtotime($nowTime);
                $data = [];
                if ($insertOpen < time() + 3 && $insertOpen > time()) {
                    $open = $price;
                    $productPrice = Product::db('SELECT price FROM data_' . $info['table_name'] . ' WHERE time < "' . $nowTime . '" ORDER BY time DESC LIMIT 1')->queryAll();
                    if (empty($productPrice)) {
                        $close = $price;
                    } else {
                        $close = $productPrice[0]['price'];
                    }
                    $data = [
                        'open' => $open,
                        'close' => $close,
                    ];
                }
                $maxPrice = Product::db('SELECT price FROM data_' . $info['table_name'] . ' WHERE time > "' . $nowTime . '" ORDER BY price DESC LIMIT 1')->queryAll();
                $minPrice = Product::db('SELECT price FROM data_' . $info['table_name'] . ' WHERE time > "' . $nowTime . '" ORDER BY price ASC LIMIT 1')->queryAll();
                if (empty($maxPrice)) {
                    $data['high'] = $price;
                    $data['low'] = $price;
                } else {
                    $data['high'] = $maxPrice[0]['price'];
                    $data['low'] = $minPrice[0]['price'];
                }
                $data['price'] = $price;
                $data['time'] = date('Y-m-d H:i:s', time());
                $this->insert($info['table_name'], $data);
            } else {
                // 每个品类，先采集最新价格
                $result = $this->getHtml($info['url']);
                if ($result) {
                    preg_match('/.*?\[(.*?)\]/Ui', $result, $match);
                    try {
                        $data = json_decode($match[1]);
                    } catch (\Exception $e) {
                        continue;
                    }
                    $price = $data->now;
                    if (!$price) {
                        continue;
                    }
                    if (in_array($tableName, ['conc', 'xau'])) {
                        $price = $price * 100;
                        $data->now = $data->now * 100;
                        $data->open = $data->open * 100;
                        $data->high = $data->high * 100;
                        $data->low = $data->low * 100;
                        $data->close = $data->close * 100;
                    }
                    if (isset($data->updatetime)) {
                        $time = $data->updatetime;
                    } else {
                        $time = $data->pricetime;
                    }
                    $info = [
                        'price' => $price,
                        'open' => $data->open,
                        'high' => $data->high,
                        'low' => $data->low,
                        'close' => $data->close,
                        'diff' => $data->updrop,
                        'diff_rate' => $data->percent,
                        'time' => date('Y-m-d H:i:s', strtotime($time))
                    ];
                    $this->insert($tableName, $info);
                }
            }
        }

        // 监听是否有人应该平仓
        $this->listen();
    }

    protected function getHtml($url, $options = null)
    {
        $options[CURLOPT_HTTPHEADER] = ['Referer: http://www.xftz.cn/hq/ygy9995_utf8.php'];
        return Curl::get($url, $options);
    }
}
