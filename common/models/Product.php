<?php

namespace common\models;

use Yii;

/**
 * 这是表 `product` 的模型
 */
class Product extends \common\components\ARModel
{
    const HOT_YES = 1;
    const HOT_NO = -1;

    const FORCE_SELL_YES = 1;
    const FORCE_SELL_NO = -1;

    const CURRENCY_RMB = 1;
    const CURRENCY_USD = 2;
    const CURRENCY_AUD = 3;//澳元
    const CURRENCY_CAD = 4;//加元
    const CURRENCY_HKD = 5;//港币
    const CURRENCY_EUR = 6;//欧元
    const CURRENCY_GBP = 7;//英镑
    //const CURRENCY_AUD = 8;//加元
    //const CURRENCY_AUD = 9;//加元


    const ON_SALE_YES = 1;
    const ON_SALE_NO = -1;

    const SOURCE_TRUE = 1;
    const SOURCE_FALSE = 2;
    
    //交易类型
    const BUY_TYPE_CASH = 1;
    const BUY_TYPE_VOLUME = 2;

    public function rules()
    {
        return [
            [['table_name', 'name', 'deposit', 'one_profit'], 'required'],
            [['deposit', 'fee'], 'number'],
            [['unit','unit_price','maxrise','maxlost','unit_price_na'],'required'],
            [['one_profit', 'is_trade', 'force_sell', 'currency', 'hot', 'type', 'on_sale', 'state'], 'integer'],
            [['trade_time', 'play_rule'], 'default', 'value' => ''],
            [['table_name', 'name'], 'string', 'max' => 50],
           
            [['rest_day','desc'], 'string', 'max' => 255]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => '产品对应表名',
            'name' => '产品名称',
            'deposit' => '保证金',
            'one_profit' => '一手盈亏',
            'desc' => '产品描述',
            'fee' => '手续费',
            'unit'=>'波动单位',
            'unit_price'=>'波动单位人民币元',
            'maxrise'=>'最大止盈',
            'maxlost'=>'最大止损',
            'unit_price_na'=>'波动单位外币元',
            'trade_time' => '交易时间',
            'is_trade' => '允许交易',
            'rest_day' => '休市日',
            'play_rule' => '玩法规则',
            'force_sell' => '是否强制平仓：1是，-1否',
            'currency' => '币种： 1人民币，2美元',
            'hot' => '是否是热门期货：1是，-1不是',
            'type' => '期货类别：1国内，2国外',
            'on_sale' => '上架状态：1上架，-1下架',
            'state' => '状态',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getDataAll()
    {
        return $this->hasOne(DataAll::className(), ['name' => 'table_name']);
    }

    public function getPriceExtend()
    {
        return $this->hasMany(ProductPrice::className(), ['product_id' => 'id']);
    }

    public function getProductParam()
    {
        return $this->hasOne(ProductParam::className(), ['product_id' => 'id']);
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'product.id' => $this->id,
                'product.deposit' => $this->deposit,
                'product.one_profit' => $this->one_profit,
                'product.fee' => $this->fee,
                'product.is_trade' => $this->is_trade,
                'product.force_sell' => $this->force_sell,
                'product.currency' => $this->currency,
                'product.hot' => $this->hot,
                'product.type' => $this->type,
                'product.on_sale' => $this->on_sale,
                'product.state' => $this->state,
            ])
            ->andFilterWhere(['like', 'product.table_name', $this->table_name])
            ->andFilterWhere(['like', 'product.name', $this->name])
            ->andFilterWhere(['like', 'product.desc', $this->desc])
            ->andFilterWhere(['like', 'product.trade_time', $this->trade_time])
            ->andFilterWhere(['like', 'product.rest_day', $this->rest_day])
            ->andFilterWhere(['like', 'product.play_rule', $this->play_rule])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    /**
     * 获取最新一条期货价格数组
     * @access public
     * @return array
     */
    public static function newProductPrice($name)
    {
        //判断不是不是数字
        if (is_numeric($name)) {
            $product = Product::findOne($name);
            if (!empty($product)) {
                $name = $product->table_name;
            }
        }
        $table = 'data_' . $name;
        $sql = "SELECT * FROM $table WHERE id =(SELECT MAX(id) FROM $table)";
        return self::db($sql)->queryOne();
    }

    /**
     * 获取一个品类下一个交易时间（使用前先使用 isTradeTime 验证当前该品类处于休市状态）
     * 
     * @param  int|string $product 可传入产品id或是产品代号
     * @return int|false           交易时间的时间戳（如果返回`false`，表示当前处于交易时间）
     */
    public static function getNextTradeTime($productId)
    {
        if (is_numeric($productId)) {
            $model = self::findOne($productId);
        } else {
            $model = self::find()->where(['table_name' => $productId])->one();
        }
        $time = $model->trade_time;
        if (!$time) {
            $time = serialize([]);
        }
        $timeArr = unserialize($time);
        if ($timeArr) {
            $now = time();
            $mark = [];
            $mark[] = $now;
            foreach ($timeArr as $key => $item) {
                $start = strtotime(date('Y-m-d ' . $item['start'] . ':00'));
                $end = strtotime(date('Y-m-d ' . $item['end'] . ':00'));
                if ($start > $end) {
                    if ($now > $start && $now < $start + 3600 * 24 || $now > strtotime(self::$date) && $now < $end - $early) {
                        return false;
                    } else {
                        $mark[] = $start;
                    }
                } else {
                    if ($now > $start && $now < $end - $early) {
                        return false;
                    } else {
                        $mark[] = $start;
                    }
                }
            }
            sort($mark);
            $nowKey = array_search($now, $mark);
            if (isset($mark[$nowKey + 1])) {
                return $mark[$nowKey + 1];
            } else {
                return $mark[0] + 24 * 3600;
            }
        } else {
            return false;
        }
    }

    /**
     * 判断一个品类当前是否处于交易时间内
     * 
     * @param  int|string $productId 可传入产品
     * @param  int        $early     提前时间量，单位秒
     * @return boolean
     */
    public static function isTradeTime($productId, $early = 300)
    {
        $model = self::findOne($productId);
        $weekend=true;//周末是否可以买卖
        if ((date('w') == 0 && $model->source == self::SOURCE_TRUE) || (date('G') > 3 && date('w') == 6 && $model->source == self::SOURCE_TRUE)) {
            $weekend=false;
        }
        
        $time = $model->trade_time;
        if (!$time) {
            $time = serialize([]);
        }
        $timeArr = unserialize($time);
        if ($timeArr) {
            $now = time();
            foreach ($timeArr as $item) {
                $start = strtotime(date('Y-m-d ' . $item['start'] . ':00'));
                $end = strtotime(date('Y-m-d ' . $item['end'] . ':00'));
                if ($start > $end) {
                    if ($now > $start && $now < $start + 3600 * 24 || $now > strtotime(self::$date) && $now < $end - $early) {
                        if($weekend)
                        {return true;}
                        else{return false;}
                        
                    }
                } else {
                    if ($now > $start && $now < $end - $early) {
                        if($weekend)
                        {return true;}
                        else{return false;}
                    }
                }
            }
            return false;
        } else {
            if($weekend)
            {return true;}
            else{return false;}
        }
    }

    /**
     * 判断一个品类当前是否处于最后一个交易时间的结束期
     * 
     * @param  int|string $productId 传入产品id
     * @param  int        $early     提前时间量，单位秒
     * @return boolean
     */
    public static function isLastTradeTime($productId, $early = 0)
    {
        $model = self::findOne($productId);
        $time = $model->trade_time;
        if (!$time) {
            $time = serialize([]);
        }
        $timeArr = unserialize($time);
        if ($timeArr) {
            $now = time();
            $mark = [];
            foreach ($timeArr as $key => $item) {
                $start = strtotime(date('Y-m-d ' . $item['start'] . ':00'));
                $end = strtotime(date('Y-m-d ' . $item['end'] . ':00'));
                if ($start > $end) {
                    if ($now > $start && $now < $start + 3600 * 24 || $now > strtotime(self::$date) && $now < $end - $early) {
                        return false;
                    } else {
                        $mark[$start] = $key;
                    }
                } else {
                    if ($now > $start && $now < $end - $early) {
                        return false;
                    } else {
                        $mark[$start] = $key;
                    }
                }
            }
            ksort($mark);
            $lastKey = end($mark);
            $start = strtotime(date('Y-m-d ' . $timeArr[$lastKey]['start'] . ':00'));
            $end = strtotime(date('Y-m-d ' . $timeArr[$lastKey]['end'] . ':00'));
            if ($start > $end) {
                $end += 3600 * 24;
            }
            if (!$early) {
                $early = 5;
            }
            return ($now >= $end - $early) && ($now <= $end + $early);
        } else {
            return false;
        }
    }
    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/

    // Map method of field `on_sale`
    public static function getOnSaleMap($prepend = false)
    {
        $map = [
            self::ON_SALE_YES => '上架',
            self::ON_SALE_NO => '下架',
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `on_sale`
    public function getOnSaleValue($value = null)
    {
        return $this->resetValue($value);
    }

    // Map method of field `force_sell`
    public static function getForceSellMap($prepend = false)
    {
        $map = [
            self::FORCE_SELL_YES => '是',
            self::FORCE_SELL_NO => '否'
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `force_sell`
    public function getForceSellValue($value = null)
    {
        return $this->resetValue($value);
    }

    // Map method of field `currency`
    public static function getCurrencyMap($prepend = false)
    {
        $map = [
            self::CURRENCY_RMB => '人民币',
            self::CURRENCY_USD => '美元',
            self::CURRENCY_AUD => '澳元',
            self::CURRENCY_CAD => '加元',
            self::CURRENCY_HKD => '港币',
            self::CURRENCY_EUR => '欧元',
            self::CURRENCY_GBP => '英镑'
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `currency`
    public function getCurrencyValue($value = null)
    {
        return $this->resetValue($value);
    }

    // Map method of field `is_trade`
    public static function getIsTradeMap($prepend = false)
    {
        $map = [
            1 => '是',
            -1 => '否'
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `is_trade`
    public function getIsTradeValue($value = null)
    {
        return $this->resetValue($value);
    }
}
