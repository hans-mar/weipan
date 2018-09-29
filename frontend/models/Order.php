<?php

namespace frontend\models;

use Yii;

class Order extends \common\models\Order
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_date', 'end_date'], 'safe'],
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            // 'scenario' => ['field1', 'field2'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            // 'field1' => 'description1',
            // 'field2' => 'description2',
        ]);
    }

    /**
     * 二元期权模式/微盘模式——卖出产品
     * @param  int 订单id
     * @param  int 1是二元期权，2是微盘模式
     * @access public
     * @return boolean
     */
    public static function saveOrder($data, $type = 1)
    {
        if ($type == self::TYPE_EY) {
            //保证金
            $productPrice = ProductPrice::find()->where(['product_id' => $data['product_id'], 'deposit' => $data['deposit']])->one();
            if (empty($productPrice)) {
                return ['code' => -1, 'info' => '请不要输入非法数据！'];
            }
            if ($data['hand'] > $productPrice->max_hand) {
                return ['code' => -1, 'info' => '最大商品手数为'.$productPrice->max_hand.'手！'];
            }
            $point = $data['point'];
            //如果是上证指数
            // if ($productPrice->product_id == 18 || $productPrice->product_id == 19) {
            //     $data['point'] = $point / 100;
            // }
            $order = new Order();
            $order->attributes = $data;
            $order->one_profit = $order->deposit;
            $order->deposit = $order->deposit * $order->hand;
            //手续费不同
            $pointPrice = ProductPrice::find()->where(['product_id' => $data['product_id'], 'one_profit' => $point])->one();
            if (empty($pointPrice)) {
                return ['code' => -1, 'info' => '请不要输入非法数据1！'];
            }
            $order->fee = $order->deposit * $pointPrice->fee / 100;

            //查询余额是否够用
            $user = User::findModel(u()->id);
            if ($user->blocked_account < 0 || $user->account < 0) {
                return ['code' => -1, 'info' => '您的账号异常请联系管理员！'];
            }
            
            if (($user->blocked_account + $order->deposit) > $user->account) {
                return ['code' => -1, 'info' => '您的余额已不够支付，请充值！'];
            }

            $order->order_state = Order::ORDER_POSITION;
            $order->user_id = u()->id;
            //当前最新价格
            $dataAll = DataAll::newProductPrice($order->product_id);
            if (empty($dataAll)) {
                return ['code' => -1, 'info' => '最新价格数据出错！'];
            }
            if ($dataAll->price == 1) {
                return ['code' => -1, 'info' => '最新价格违法！'];
            }
            $order->price = $dataAll->price;
            $order->stop_profit_price = $data['point'];
            $order->stop_profit_point = $data['point'] + $dataAll->price;
            $order->stop_loss_price = $data['point'];
            $order->stop_loss_point = $dataAll->price - $data['point'];
            // test($order->attributes,$data);
            if (!$order->save()) {
                return ['code' => -1, 'info' => $order];
            } else {
                $user->blocked_account += $order->deposit;
                $user->save(false);
                return ['code' => 1, 'info' => '购买成功！'];
            }
        } else {       
            $productPrice = ProductPrice::find()->where(['product_id' => $data['product_id'], 'deposit' => $data['deposit']])->one();
            if (empty($productPrice)) {
                return ['code' => -1, 'info' => '请不要输入非法数据！'];
            }
            if ($data['hand'] > $productPrice->max_hand) {
                return ['code' => -1, 'info' => '最大商品手数为'.$productPrice->max_hand.'手！'];
            }
            $order = new Order();
            $order->attributes = $data;
            $order->one_profit = $productPrice->one_profit;
            $order->deposit = $order->deposit * $order->hand;
            $order->fee = $order->hand * $productPrice->fee;

            //查询余额是否够用
            $user = User::findModel(u()->id);
            if (($user->blocked_account + $order->deposit) > $user->account) {
                return ['code' => -1, 'info' => '您的余额已不够支付，请充值！'];
            }

            $order->order_state = Order::ORDER_POSITION;
            $order->user_id = u()->id;
            //当前最新价格
            $dataAll = DataAll::newProductPrice($order->product_id);
            if (empty($dataAll)) {
                return ['code' => -1, 'info' => '最新价格数据出错！'];
            }
            if ($dataAll->price == 1) {
                return ['code' => -1, 'info' => '最新价格违法！'];
            }
            $order->price = $dataAll->price;
            $order->stop_profit_price = $data['stop_profit_point'] * $order->deposit / 100;
            $order->stop_loss_price = $data['stop_loss_point'] * $order->deposit / 100;
            // test($order->attributes,$data);
            if (!$order->save()) {
                return ['code' => -1, 'info' => $order];
            } else {
                $user->blocked_account += $order->deposit;
                $user->account -= $order->fee;
                $user->save(false);
                //经纪人返点
                // UserRebate::isUserRebate($order->fee, $order->id);
                return ['code' => 1, 'info' => '购买成功！'];
            }
        }
    }


/**
     * 点位模式——保存订单
     * @param  订单数据
     * @access public
     * sim_type sim 虚拟盘，null 实盘
     * @return boolean
     */
    public static function saveDianOrder($data,$sim_type=null)
    {
            $product = Product::find()->where(['id' => $data['productId']])->one();
            //var_dump($product);
            //die();
            if (empty($product)) {
                return ['code' => -1, 'info' => '请不要输入非法数据！','data'=>$product];
            }
            if ($data['ost']/$product->unit*$product->unit_price>$data['totalMargin']) {
                return ['code' => -1, 'info' => '保证金不充足,请重新提交！'];
            }
            $dataAll = DataAll::newProductPrice($data['productId']);//当前最新价格
            if (empty($dataAll)) {
                return ['code' => -1, 'info' => '最新价格数据出错！'];
            }
             //查询余额是否够用
            $user = User::findModel(u()->id);
            if($sim_type==null)
            {
                if (($user->blocked_account + $data['totalMargin']) > $user->account) {
                return ['code' => -1, 'info' => '您的余额已不够支付，请充值！'];
                }
            }
            else
            {
                if (($user->sim_blocked_account + $data['totalMargin']) > $user->sim_account) {
                return ['code' => -1, 'info' => '您的余额已不够支付！'];
                }
            }
            

            $order = new Order();
            //$order->attributes = $data;
            if($sim_type=='sim')
            {
                $order->sim=2;
            }
            else
            {
                $order->sim=1;
            }
            
            $order->one_profit=1/$product->unit*$product->unit_price;//一个点位的盈亏数
            $order->user_id = u()->id;//用户id
            $order->product_id=$data['productId'];//产品id
            $order->hand=$data['volHand'];//手数
            $order->deposit = $data['totalMargin'];//保证金
            $order->fee = $data['totalHandlingMoney'];//手续费
            $order->stop_profit_point = $data['profit'];//止盈点位
            $order->stop_loss_point = $data['ost'];//止损点位
            $order->order_state = '1';//1持仓，2抛出
            $order->rise_fall=$data['state'];//1买涨，2买跌
            $order->created_at=date('Y-m-d H:i:s', time());//创建时间
            $order->created_by=u()->id;//创建人
            if($data['state']=="1")//买涨
            {
              $order->price = $dataAll->price;//购入价格+基本点位
              $order->stop_profit_price = $order->price+$data['profit'];//止盈
              $order->stop_loss_price = $order->price-$data['ost'];//止损
            }
            else//买跌
            {
              $order->price = $dataAll->price;//购入价格-基本点位
              $order->stop_profit_price = $order->price-$data['profit'];
              $order->stop_loss_price = $order->price+$data['ost'];
            }

            if (!$order->save()) {
                return ['code' => -1, 'info' => $order];
            } else {
                //订单保存成功，处理用户资金
                if($sim_type=='sim')//模拟盘
                {
                $user->sim_blocked_account += $order->deposit;
                $user->sim_account -= $order->fee;
                $user->save(false);

                }
                else//实盘
                {
                $user->blocked_account += $order->deposit;
                $user->account -= $order->fee;
                $user->save(false);
                //echo 'hehe';
                //exit;
                //经纪人返点
                 UserRebate::isUserRebate($order);
                }
                
                
                
                return ['code' => 1, 'info' => '购买成功！'];
            }
        
    }



    /**
     * 盈利钱数
     * @param  int|model 订单id/model
     * @access public
     * @return number 钱数
     */
    public static function userWinOrder($order)
    {
        if (is_numeric($order)) {
            $order = self::findModel($order);
        }

        $dataAll = DataAll::newProductPrice($order->product_id);
        if ($order->rise_fall == self::RISE) {
        //钱数 （当前价格-购买价格）*手数*每个点的差价
            $diffPrice = sprintf('%.3f', $dataAll->price - $order->price);
        } else {
        //买跌
            $diffPrice = sprintf('%.3f', $order->price - $dataAll->price);
        }
        //汇率
        // $rate = 1;
        // //判断期货是否属于人民币
        // if ($order->product->currency == self::CUR_USA) {
        //     $rate = config()->get('usa', self::USA_RATE);
        // }
        //盈利多少钱
        return sprintf('%.2f', ($diffPrice * $order->one_profit * $order->hand));
    }

    /**
     * 订单最新的数据
     * @param  int 订单id
     * @access public
     * @return arraycover
     */
    public static function getUserOrderData($id)
    {
        $order = self::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id, 'id' => $id])->one();
        $data['profit'] = self::userWinOrder($order);
        $data['price'] = DataAll::newProductPrice($order->product_id)->price;
        $data['profitRate'] = $data['profit'] * 100 / $order->deposit;
        $data['deposit'] = $data['profit'] + $order->deposit;
        return $data;
    }

    //经纪人客户平仓时间搜索+产品
    public function coverQuery($array)
    {
        $this->load(get());
        $this->start_date = $this->start_date ?: date('Y-m-d', strtotime('-31 days'));
        $this->end_date = $this->end_date ?: date('Y-m-d', strtotime('+1 days'));
        return $this->search()
                    ->joinWith(['product', 'user'])
                    ->andWhere(['order_state' => Order::ORDER_THROW])
                    ->andWhere(['in', 'user_id', $array])
                    ->andFilterWhere(['between', 'order.created_at', $this->start_date, $this->end_date])
                    ->andFilterWhere(['product_id' => $this->product_id])
                    ->orderBy('order.created_at DESC');
    }
}
