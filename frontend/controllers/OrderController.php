<?php
 
namespace frontend\controllers;

use Yii;
use frontend\models\User;
use frontend\models\Product;
use frontend\models\Order;
use frontend\models\ProductPrice;
use frontend\models\Coupon;
use frontend\models\UserCoupon;
use frontend\models\DataAll;

class OrderController extends \frontend\components\Controller
{
    public function beforeAction($action)
    {
        //$actions = ['ajax-update-status', 'wxtoken', 'wxcode', 'test', 'rule', 'captcha','notify', 'hx-weixin', 'zynotify', 'update-user', 'update', 'tynotify','login','reg','verify-code','ajax-reg','ajax-forget','pass-for-get'];
        //        var_dump(user()->isGuest);
        //        var_dump(!in_array($this->action->id, $actions));
        // exit;
           if(user()->isGuest)
           {
               $this->redirect(['/site/login']);
               return false;
           }
           else{
               return true;


           }
        // if (user()->isGuest) {
        //     $wx = session('wechat_userinfo');
        //     if (!empty($wx)) {
        //         $user = User::find()->where(['open_id' => $wx['openid']])->one();
        //         $user->login(false);
        //     } else {
        //         $code = get('code');
        //         if (empty($code)) {
        //             $this->redirect(['/wechart.php']);
        //             return false;
        //         } else {
        //             User::registerUser($code);
        //         }
        //     }
        // }
        // if (!parent::beforeAction($action)) {
        //     return false;
        // } else {
        //     return true;
        // }
    }

    //下单
    public function actionIndex()
    {
        $this->view->title = '下单';
        $pid = req('pid');
        if (user()->isGuest) {
            return $this->redirect(['site/login']);
        }
        $product = Product::find()->andWhere(['id' => $pid])->with('dataAll')->one();
        $productPrice = ProductPrice::getSetProductPrice($product->id);
        if (!isset($productPrice)) {
            return $this->redirect(['site/wrong']);
        }
        //体验卷
        $couponType = UserCoupon::getNumberType($pid);
        return $this->render('index', compact('product', 'productPrice', 'couponType'));
    }

    //持仓
    public function actionPosition()
    {

        $this->view->title = '持仓';
        if (user()->isGuest) {
            return $this->redirect(['site/login']);
        }
        $orders = Order::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id])->with('product')->orderBy('created_at DESC')->all();

        return $this->render('position', compact('orders'));
    }
    //持仓改变止盈止损
     public function actionHoldChange()
    {
        $orderid=get('orderid');

        return $this->renderPartial('holdChange',compact('orderid'));
    }
    //保存持仓改变止盈止损
    public function actionSaveHoldChange()
    {
        $data=post();
        $order= Order::find()->where(['id'=>$data['orderId']])->one();
        if(empty($order))
        {
            return error("无此订单");
        }
        if($order->stop_profit_point<$data['moneyProfix'] or $order->stop_loss_point<$data['moneyOst'])
        {
            return error("改变值超过最大值！");
        }
        else{
            if($order->rise_fall=='1')
            {
                $order->stop_profit_price=$order->stop_profit_price-$order->stop_profit_point+$data['moneyProfix'];//止盈金额
                $order->stop_loss_price=$order->stop_loss_price+$order->stop_loss_point-$data['moneyOst'];//止损金额
            }
            else
            {
                $order->stop_profit_price=$order->stop_profit_price+$order->stop_profit_point-$data['moneyProfix'];
                $order->stop_loss_price=$order->stop_loss_price-$order->stop_loss_point+$data['moneyOst'];

            }
            
            $order->stop_profit_point=$data['moneyProfix'];
            $order->stop_loss_point=$data['moneyOst'];
            $order->save(false);
            return success("订单改变已保存");
        }
        


    }
    





     //根据订单号返回订单详情
     public function actionAjaxOrderDetail()
    {

        $order = Order::find()->where(['id' => get('orderId'), 'user_id' => u()->id])->with('product')->orderBy('created_at DESC')->asArray()->one();
        $order['unit']=(double)$order['product']['unit'];
        $order['stop_profit_point']=(double)$order['stop_profit_point'];
        $order['stop_loss_point']=(double)$order['stop_loss_point'];

        return success("",$order);
    }
    public function actionGetHoldCount()//返回持仓订单数量
    {
        if(session('sim_type')=='sim')
        {
            $count=Order::find()->where(['order_state'=>1,'user_id'=>u()->id,'sim'=>2])->count();

        }
        else
        {

            $count=Order::find()->where(['order_state'=>1,'user_id'=>u()->id,'sim'=>1])->count();

        }
        //$count=Order::find()->where(['order_state'=>1,'user_id'=>u()->id])->count();
        if(empty($count))
        {
            return success("",0);
        }
        else
        {
            return success("",(int)$count);
        }
        
    }

//点位手动平仓ajax
public function actionAjaxSellPosition()
    {
        //return error(session('sim_type'));
        $orderid=get('orderId');

        if(empty($orderid))
            {return error("订单不存在！");}

        if(Order::sellOrderHand($orderid,false,session('sim_type')))
        {
           return success("成功平仓！");
        }
        else
        {
           return error("平仓失败！");
        }
    }


    //平仓
    public function actionSellPosition($id)
    {
        //$this->view->title = '平仓';
        if (user()->isGuest) {
            return $this->redirect(['site/login']);
        }
        $order = Order::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id, 'id' => $id])->with('product')->one();
        if (empty($order)) {
            return $this->redirect(['site/index']);
        }
        return $this->render('sellPosition', compact('order'));
    }

    /**
     * 更新所有持仓单数据ajax请求
     * @access public
     * @return json
     */
    public function actionAjaxUpdateOrder()
    {
        $orders = Order::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id])->all();
        $data = [];
        foreach ($orders as $order) {
            $data[$order->id] = Order::userWinOrder($order);
        }
        return success($data);
    }

    /**
     * 更新一条持仓单数据ajax请求
     * @access public
     * @return json
     */
    public function actionAjaxUpdateOrderOne()
    {
        $data = Order::getUserOrderData(post('id'));
        // test($data);
        return success($data);
    }

    /**
     * 保存订单
     * @access public
     * @return json
     */
    public function actionAjaxSaveOrder()
    {

        if (user()->isGuest) {
            return $this->redirect(['site/login']);
        }
        // return error('测试阶段，请勿下单！');

        $data = post('data');
        //var_dump($data);die;
        
        $product = Product::findModel($data['product_id']);
        //特殊产品周末正常
        if ((date('w') == 0 && $product->source == Product::SOURCE_TRUE) || (date('G') > 3 && date('w') == 6 && $product->source == Product::SOURCE_TRUE)) {
            return error('周末休市，无法委托买入！');
        }
        //判断此期货是否在商品时间内
        if (!Product::isTradeTime($data['product_id'])) {
            return error('非买入时间，无法委托买入！');
        }
        if ($data['totalMargin'] <= 0||$data['totalMargin'] == null) {
            return error('商品保证金不能为空！');
        }
        if ($data['ost']==null||$data['profit']==null) {
            return error('止损止盈点位不能为空');
        }
        $order = Order::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id, 'product_id' => $data['product_id']])->orderBy('created_at DESC')->one();
        $time = $order?time() - strtotime($order->created_at):0;  

        if ($time <= 10 && $time != 0) {
            return error('下单后10秒之内不能再次下单！');
        }
        //商品单日最高持仓金额50000
        $maxOrder = Order::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id, 'product_id' => $data['product_id']])->andWhere(['>', 'created_at', date('Y-m-d 00:00:00', time())])->andWhere(['<', 'created_at', date('Y-m-d 00:00:00', strtotime('tomorrow'))])->with('product')->select('SUM(deposit) deposit')->one();
        if ($maxOrder->deposit > 50000) {
            return error('商品单日最高持仓金额不能超过50000！');
        }
        //订单处理
        $res = Order::saveOrder($data);
        if ($res['code'] == 1) {
            //取出订单信息
            $orders = Order::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id, 'product_id' => $data['product_id']])->andWhere(['>', 'created_at', date('Y-m-d 00:00:00', time())])->with('product')->orderBy('created_at DESC')->all();
            return success($this->renderPartial('/site/_orderList', compact('orders')), $data['deposit'] * $data['hand']);
            // return success();
        } else {
            return error($res['info']);
        }
    }


    

    /**
     * 保存点位订单
     * @access public
     * @return json
     */
    public function actionSaveOrder()
    {
        $enableCsrfValidation = false;
        if (user()->isGuest) {
            return $this->redirect(['site/login']);
        }
        // return error('测试阶段，请勿下单！');

        $data = post();
        // var_dump($data);die;
        $product = Product::findModel($data['productId']);


        //特殊产品周末正常
        if ((date('w') == 0 && $product->source == Product::SOURCE_TRUE) || (date('G') > 3 && date('w') == 6 && $product->source == Product::SOURCE_TRUE)) {
            return error('周末休市，无法委托买入！');
        }
        //判断此期货是否在商品时间内
        if (!Product::isTradeTime($data['productId'])) {
            return error('非买入时间，无法委托买入！');
        }
        if ($data['volHand'] <= 0) {
            return error('商品手数不能小于等于0！');
        }
        if ($data['totalMargin'] <= 0||$data['totalMargin'] == null) {
            return error('商品保证金不能为空！');
        }
        if ($data['ost']==null||$data['profit']==null) {
            return error('止损止盈点位不能为空');
        }
        /*$order = Order::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id, 'product_id' => $data['product_id']])->orderBy('created_at DESC')->one();
        $time = $order?time() - strtotime($order->created_at):0;  

        if ($time <= 10 && $time != 0) {
            return error('下单后10秒之内不能再次下单！');
        }*/
        //商品单日最高持仓金额50000
        /*$maxOrder = Order::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id, 'product_id' => $data['product_id']])->andWhere(['>', 'created_at', date('Y-m-d 00:00:00', time())])->andWhere(['<', 'created_at', date('Y-m-d 00:00:00', strtotime('tomorrow'))])->with('product')->select('SUM(deposit) deposit')->one();
        if ($maxOrder->deposit > 50000) {
            return error('商品单日最高持仓金额不能超过50000！');
        }*/
        //订单处理
        $res = Order::saveDianOrder($data,session('sim_type'));
        if ($res['code'] == 1) {
            //取出订单信息
            /*$orders = Order::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id, 'product_id' => $data['product_id']])->andWhere(['>', 'created_at', date('Y-m-d 00:00:00', time())])->with('product')->orderBy('created_at DESC')->all();*/
            return success('订单保存成功！');
            // return success();
        } else {
            return error($res['info']);
        }
    }

    /**
     * 平仓订单ajax请求
     * @access public
     * @return json
     */
    public function actionAjaxSellOrder()
    {
        if (user()->isGuest) {
            return $this->redirect(['site/login']);
        }
        $order = Order::find()->where(['id' => post('id'), 'order_state' => Order::ORDER_POSITION, 'user_id' => u()->id])->one();
        if (empty($order)) {
            return error('此订单已被系统平仓！');
        }
        $bool = Order::sellOrder($order->id);
        if ($bool) {
            self::success('平仓成功！');
        } else {
            self::error('订单数据不存在！');
        }
    }
    
    /**
     * 平仓订单ajax请求
     * @access public
     * @return json
     */
    public function actionSo()
    {
        $bool = Order::sellOrder(10);
        if ($bool) {
            self::success('平仓成功！');
        } else {
            self::error('订单数据不存在！');
        }
    }
}
