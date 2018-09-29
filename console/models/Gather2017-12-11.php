<?php

namespace console\models;

use Yii;
use common\models\Order;
use common\models\Product;
use common\models\DataAll;
use common\helpers\Curl;
use common\helpers\StringHelper;

class Gather extends \yii\base\Object
{
    use \common\traits\ChisWill;

    public $productList = [];
    protected $updateMap = [];
    protected $switchMap = [];
    protected $faker;

    public function init()
    {
        parent::init();

        $this->productList = array_intersect_key($this->productList, array_flip(config('productList')));
    }

    protected function uniqueInsert($name, $data)
    {
        $row = self::db("SELECT
            price,
            time
        FROM
            data_{$name}
        ORDER BY
            id DESC
        LIMIT 1")->queryOne();
        // 价格不同或是间隔10s
        if ($row['price'] != $data['price'] || strtotime($data['time']) - strtotime($row['time']) >= 10) {
            $this->insert($name, $data);
        }
    }

    protected function insert($name, $data)
    {
        try {
            // 是否开启作弊模式
            if (($switch = option('risk_switch')) && isset($this->switchMap[$name])) {
                $riskProduct = option('risk_product');
                if (isset($riskProduct) && $riskProduct[$name] == 1) {
                    $riseQuery = Order::find()->joinWith('product')->where(['order_state' => Order::ORDER_POSITION, 'product.table_name' => $name])->select('SUM(order.deposit) hand');
                    $downQuery = clone $riseQuery;
                    $riseQuery->andWhere(['rise_fall' => Order::RISE]);
                    $downQuery->andWhere(['rise_fall' => Order::FALL]);
                    $rise = $riseQuery->one()->hand ?: 0;
                    $down = $downQuery->one()->hand ?: 0;
                    if ($rise != $down) {
                        $wave = $rise > $down ? -1 : 1;
                        if (strpos($data['price'], '.') !== false) {
                            list($int, $point) = explode('.', $data['price']);
                            $point = pow(10, -1 * strlen($point));
                        } else {
                            $point = 1;
                        }
                        // 获取行情信息
                        $dataInfo = DataAll::findOne($name);
                        $data['price'] = $dataInfo->price;
                        $data['price'] += $point * $wave * intval(mt_rand(50, 190) / 50);
                    }
                }
            }
            if (self::dbInsert('data_' . $name, ['price' => $data['price'], 'time' => $data['time']])) {
                $this->updateMap[$name] = $data;
            } else {
               self::log($data, 'gather/' . $name);
            }
        } catch (\yii\db\IntegrityException $e) {
            // 唯一索引冲突才会进这，什么都不必做
        }
    }

    protected function afterInsert()
    {
        $priceJson = @file_get_contents(Yii::getAlias('@frontend/web/price.json')) ?: '{}';
        $priceJson = json_decode($priceJson, true);
        foreach ($this->updateMap as $tableName => $info) {
            // 更新 data_all 的最新价格
            self::dbUpdate('data_all', $info, ['name' => $tableName]);
            // 将所有更新的价格写入文件
            $priceJson[$tableName] = $info['price'];
        }
        file_put_contents(Yii::getAlias('@frontend/web/price.json'), json_encode($priceJson));
    }

    protected function listen()
    {
        $this->afterInsert();
        // 更新所有持仓订单的浮亏
         self::db('  UPDATE
                         `order` o,
                         product p,
                         data_all a
                     SET
                        sell_price = a.price,
                       profit = IF (
                             o.rise_fall = ' . Order::RISE . ',
                             a.price - o.price,
                             o.price - a.price
                        ) * o.hand * o.one_profit
                    WHERE
                        a.name = p.`table_name`
                     AND o.product_id = p.id
                     AND o.order_state =  ' . Order::ORDER_POSITION . '
                    AND sell_price != a.price')
         ->execute();
        // 获取所有品类当前交易状态
        //$productMap = $this->getAllTradeTime();
       // $extra = [];
       // foreach ($productMap as $product => $isTrade) {
        //    if ($isTrade === true) {
         //       $extra[] = $product;
         //   }
        //}
        //if ($extra) {
        //    $extraWhere = ' OR (order_state = ' . Order::ORDER_POSITION . ' and product_id in (' . implode(',', $extra) . ') )';
        //} else {
        //    $extraWhere = '';
        //}
        // 获取所有止盈止损订单ID
		$extraWhere = '';
         $ids = self::db('SELECT id from `order` where (order_state = ' . Order::ORDER_POSITION . ' AND (
            profit + deposit <= 0 OR (profit <= stop_loss_point * -1 *one_profit  AND stop_loss_point <> 0) OR (profit >= stop_profit_point*one_profit AND stop_profit_point <> 0)))' . $extraWhere)->queryAll();
        array_walk($ids, function ($value) {
            Order::sellOrderHand($value['id'], true);//点位平仓操作
         });
        //$ids = self::db('SELECT o.id, a.price FROM `order` o INNER JOIN product p on p.id = o.product_id INNER JOIN data_all a on a.name = p.table_name where 
           // (order_state = ' . Order::ORDER_POSITION . ' AND ((a.price >= stop_profit_point) OR (a.price <= stop_loss_point)))' . $extraWhere)->queryAll();
       // array_walk($ids, function ($value) {
           // Order::sellOrder($value['id'], $value['price']);
       // });
    }

    protected function getAllTradeTime()
    {
        $data = [];
        $products = Product::find()->where(['force_sell' => Product::FORCE_SELL_YES])->select(['id'])->asArray()->all();
        foreach ($products as $product) {
            $data[$product['id']] = Product::isLastTradeTime($product['id'], 60);
        }
        return $data;
    }

    protected function getHtml($url, $data = null)
    {
        return Curl::get($url, [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
    }
}
