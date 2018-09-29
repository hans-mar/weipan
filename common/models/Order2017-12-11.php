<?php

namespace common\models;

use Yii;

/**
 * 这是表 `order` 的模型
 */
class Order extends \common\components\ARModel
{
    // 1买，2卖
    const BUY = 1;
    const SELL = 2;
    // 1是二元期权2是微盘
    const TYPE_EY = 1;
    const TYPE_WP = 2;
    // 持仓状态，1持仓，2抛出
    const ORDER_POSITION = 1;
    const ORDER_THROW = 2;
    //涨跌（1涨2跌）
    const RISE = 1;
    const FALL = 2;
    // 币种 1RMB，2USD
    const CURRENCY_RMB = 1;
    const CURRENCY_USD = 2;
    // 是否系统平仓
    const IS_CONSOLE_YES = 1;
    const IS_CONSOLE_NO = -1;
    // 默认美元汇率
    const USA_RATE = 6.77;

    public $product_name;
    public $start_date;
    public $end_date;
    
    public function rules()
    {
        return [
            [['user_id', 'product_id', 'hand', 'price'], 'required'],
            [['user_id', 'product_id', 'hand', 'rise_fall', 'sell_hand', 'currency', 'order_state', 'created_by', 'updated_by'], 'integer'],
            [['price', 'one_profit', 'fee', 'stop_profit_price', 'stop_profit_point', 'stop_loss_price', 'stop_loss_point', 'deposit', 'sell_price', 'sell_deposit', 'discount', 'profit'], 'number'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'product_id' => '买卖品类',
            'hand' => '手数',
            'price' => '买入价',
            'one_profit' => '一手盈亏',
            'fee' => '手续费',
            'stop_profit_price' => '止盈金额',
            'stop_profit_point' => '止盈点数',
            'stop_loss_price' => '止损金额',
            'stop_loss_point' => '止损点数',
            'deposit' => '保证金',
            'rise_fall' => '涨跌：1涨，2跌',
            'sell_price' => '卖出价格',
            'sell_hand' => '卖出手数',
            'sell_deposit' => '卖出总价',
            'discount' => '优惠金额',
            'currency' => '币种：1人民币，2美元',
            'profit' => '盈亏',
            'order_state' => '持仓状态，1持仓，2抛出',
            'created_at' => '下单时间',
            'created_by' => 'Created By',
            'updated_at' => '平仓时间',
            'updated_by' => 'Updated By',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'order.id' => $this->id,
                'order.user_id' => $this->user_id,
                'order.product_id' => $this->product_id,
                'order.hand' => $this->hand,
                'order.price' => $this->price,
                'order.one_profit' => $this->one_profit,
                'order.fee' => $this->fee,
                'order.stop_profit_price' => $this->stop_profit_price,
                'order.stop_profit_point' => $this->stop_profit_point,
                'order.stop_loss_price' => $this->stop_loss_price,
                'order.stop_loss_point' => $this->stop_loss_point,
                'order.deposit' => $this->deposit,
                'order.rise_fall' => $this->rise_fall,
                'order.sell_price' => $this->sell_price,
                'order.sell_hand' => $this->sell_hand,
                'order.sell_deposit' => $this->sell_deposit,
                'order.discount' => $this->discount,
                'order.currency' => $this->currency,
                'order.profit' => $this->profit,
                'order.order_state' => $this->order_state,
                'order.created_by' => $this->created_by,
                'order.updated_by' => $this->updated_by,
            ])
            ->andFilterWhere(['like', 'order.created_at', $this->created_at])
            ->andFilterWhere(['like', 'order.updated_at', $this->updated_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/


    /**
     * 点位模式——卖出产品
     * @param  int 订单id
     * @param  float 产品当前价格
     * @access public
     * @return boolean
     */
    public static function sellOrderHand($id,$isConsole=false)
    {
        $query = self::find()->where(['id' => $id])->with('product');
        if (!$isConsole) {
            $query->andWhere(['user_id' => u()->id]);
        }
        $order = $query->one();
        if (!empty($order)) {
            //最新价格
            $dataAll = DataAll::newProductPrice($order->product_id);
            //$product=Product::find()->where(['id'=>product_id])->one();
            // $dataAll->price = 200;
            //买涨
            if ($order->rise_fall == self::RISE) {
                //钱数 （当前价格-购买价格）*手数*每个点的差价
                $diffPrice = sprintf('%.3f', $dataAll->price - $order->price);
            } else {
            //买跌
                $diffPrice = sprintf('%.3f', $order->price - $dataAll->price);
            }

            //挣了多少钱
            $order->profit = sprintf('%.2f', ($diffPrice * $order->one_profit * $order->hand));
            //如果平仓的时候收益超出，按设定最高收益
            if ($order->profit > 0) {
                if ($order->stop_profit_point > 0) {
                    //盈利不能超过设置盈利
                    if ($order->profit > $order->hand*$order->one_profit*$order->stop_profit_point) {
                        $order->profit = $order->hand*$order->one_profit*$order->stop_profit_point;
                    }
                }
            } else {
                if ($order->stop_loss_point > 0) {
                    //亏损不能超过设置亏损
                    if (-$order->profit > $order->hand*$order->one_profit*$order->stop_loss_point) {
                        $order->profit = -($order->hand*$order->one_profit*$order->stop_loss_point);
                    }
                }
            }
            //卖掉收入
            $order->sell_deposit = sprintf('%.2f', $order->deposit + $order->profit);
            //防止爆仓
            if ($order->sell_deposit < 0) {
                $order->sell_deposit = 0;
                $order->profit = -$order->deposit;
            }
            $order->sell_hand = $order->hand;
            $order->sell_price = $dataAll->price;
            $order->order_state = self::ORDER_THROW;
            $order->is_console = $isConsole === true ? self::IS_CONSOLE_YES : self::IS_CONSOLE_NO;

            if ($order->save()) {
                //去除该单用户的冻结资金 增加钱数 (用户是否用现金支付fee等于0用了体验卷)
                $user = User::findOne($order->user_id);
                $user->account += $order->profit;
                $user->blocked_account -= $order->deposit;

                if ($order->profit > 0) {
                    $user->profit_account += $order->profit;
                } else {
                    $user->loss_account += $order->profit;
                }
                $user->save(false); 
                return true;
            }
        }
        return false;
    }

    /**
     * 二元期权模式——卖出产品
     * @param  int 订单id
     * @param  float 产品当前价格
     * @access public
     * @return boolean
     */
    public static function sellOrder($id, $price = 0)
    {
        $order = self::find()->where(['id' => $id, 'order_state' => self::ORDER_POSITION])->with('product')->one();

        if (!empty($order) && !empty($price)) {
            if ($price == 0) {
                return false;
            }
            //买涨
            if ($order->rise_fall == self::RISE) {
                if ($price >= $order->stop_profit_point) {
                    //赢钱平仓
                    $profit = sprintf('%.2f', $order->deposit - $order->fee * 2);
                    $order->profit = $profit + $order->fee;                
                    $order->sell_deposit = sprintf('%.2f', $order->deposit + $order->profit);
                } elseif ($price <= $order->stop_loss_point) {
                    //亏钱平仓
                    $order->profit = sprintf('%.2f', ($order->fee - $order->deposit));
                    $order->sell_deposit = 0;
                } else {
                    //保本平仓
                    $order->profit = $profit = 0;
                    $order->sell_deposit = $order->deposit;
                }
            } else {
                if ($price >= $order->stop_profit_point) {
                    //亏钱平仓
                    $order->profit = sprintf('%.2f', ($order->fee - $order->deposit));
                    $order->sell_deposit = 0;
                } elseif ($price <= $order->stop_loss_point) {
                    //赢钱平仓
                    $profit = sprintf('%.2f', $order->deposit - $order->fee * 2);
                    $order->profit = $profit + $order->fee;
                    $order->sell_deposit = sprintf('%.2f', $order->deposit + $order->profit);
                } else {
                    //保本平仓
                    $order->profit = $profit = 0;
                    $order->sell_deposit = $order->deposit;
                }
            }

            $order->sell_hand = $order->hand;
            if ($order->profit != 0) {
                if ($price > $order->price) {
                   $order->sell_price = $order->price + $order->stop_profit_price; 
                } else {
                   $order->sell_price = $order->price - $order->stop_profit_price; 
                }
            }
            // $order->sell_price = $price;
            $order->order_state = self::ORDER_THROW;
            $order->is_console = 1;
            if ($order->save()) {
                //去除该单用户的冻结资金 增加钱数
                $user = User::findOne($order->user_id);
                $user->blocked_account -= $order->deposit;

                if ($order->profit != 0) {
                    UserRebate::isUserRebate($order);
                    //综会头寸
                    AdminDeposit::depositRecord($order);
                }
                if ($order->profit >= 0) {
                    $user->account += $profit;
                    $user->profit_account += $order->profit;
                } else {
                    $user->account -= $order->deposit; 
                    $user->loss_account += $order->profit;
                }
                $user->save(false); 
                return true;
            }
        }
        return false;
    }

    /**
     * 微交易模式——卖出产品
     * @param  int 订单id
     * @access public
     * @return boolean
     */
    public static function sellOrderStop($id, $isConsole = false)
    {
        $query = self::find()->where(['id' => $id])->with('product');
        if (!$isConsole) {
            $query->andWhere(['user_id' => u()->id]);
        }
        $order = $query->one();
        if (!empty($order)) {
            //最新价格
            $dataAll = DataAll::newProductPrice($order->product_id);
            // $dataAll->price = 200;
            //买涨
            if ($order->rise_fall == self::RISE) {
                //钱数 （当前价格-购买价格）*手数*每个点的差价
                $diffPrice = sprintf('%.3f', $dataAll->price - $order->price);
            } else {
            //买跌
                $diffPrice = sprintf('%.3f', $order->price - $dataAll->price);
            }

            //挣了多少钱
            $order->profit = sprintf('%.2f', ($diffPrice * $order->one_profit * $order->hand));
            //如果平仓的时候收益超出，按设定最高收益
            if ($order->profit > 0) {
                if ($order->stop_profit_point > 0) {
                    //盈利不能超过设置盈利
                    if ($order->profit > $order->stop_profit_price ) {
                        $order->profit = $order->stop_profit_price;
                    }
                }
            } else {
                if ($order->stop_loss_point > 0) {
                    //亏损不能超过设置亏损
                    if (-$order->profit > $order->stop_loss_price) {
                        $order->profit = -$order->stop_loss_price;
                    }
                }
            }
            //卖掉收入
            $order->sell_deposit = sprintf('%.2f', $order->deposit + $order->profit);
            //防止爆仓
            if ($order->sell_deposit < 0) {
                $order->sell_deposit = 0;
                $order->profit = -$order->deposit;
            }
            $order->sell_hand = $order->hand;
            $order->sell_price = $dataAll->price;
            $order->order_state = self::ORDER_THROW;
            $order->is_console = $isConsole === true ? self::IS_CONSOLE_YES : self::IS_CONSOLE_NO;
// test($order->attributes);
            if ($order->save()) {
                //去除该单用户的冻结资金 增加钱数 (用户是否用现金支付fee等于0用了体验卷)
                $user = User::findOne($order->user_id);
                $user->account += $order->profit;
                $user->blocked_account -= $order->deposit;

                if ($order->profit > 0) {
                    $user->profit_account += $order->profit;
                } else {
                    $user->loss_account += $order->profit;
                }
                $user->save(false); 
                return true;
            }
        }
        return false;
    }

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/

    // Map method of field `order_state`
    public static function getOrderStateMap($prepend = false)
    {
        $map = [
            self::ORDER_POSITION => '持仓中',
            self::ORDER_THROW => '已结算',
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `order_state`
    public function getOrderStateValue($value = null)
    {
        return $this->resetValue($value);
    }

    // Map method of field `rise_fall`
    public static function getRiseFallMap($prepend = false)
    {
        $map = [
            self::RISE => '买涨',
            self::FALL => '买跌'
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `rise_fall`
    public function getRiseFallValue($value = null)
    {
        return $this->resetValue($value);
    }

    // Map method of field `currency`
    public static function getCurrencyMap($prepend = false)
    {
        $map = [
            self::CURRENCY_RMB => '人民币',
            self::CURRENCY_USD => '美元'
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `currency`
    public function getCurrencyValue($value = null)
    {
        return $this->resetValue($value);
    }

    public static function getProductNameMap($prepend = false)
    {
        $map = Product::find()->andWhere(['on_sale' => Product::ON_SALE_YES])->map('id', 'name');

        return self::resetMap($map, $prepend);
    }

    public function getProductNameValue($value = null)
    {
        return $this->resetValue($value);
    }

    // Map method of field `is_console`
    public static function getIsConsoleMap($prepend = false)
    {
        $map = [
            self::IS_CONSOLE_YES => '是',
            self::IS_CONSOLE_NO => '否'
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `is_console`
    public function getIsConsoleValue($value = null)
    {
        return $this->resetValue($value);
    }
}
