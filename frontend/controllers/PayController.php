<?php

namespace frontend\controllers;

use Yii;
use common\helpers\Curl;
use frontend\models\User;
use frontend\models\UserCoupon;
use frontend\models\Product;
use frontend\models\Order;
use frontend\models\ProductPrice;
use frontend\models\DataAll;
use frontend\models\UserCharge;
use common\helpers\FileHelper;
use common\helpers\Json;

class PayController extends \frontend\components\Controller
{
    //支付界面
    public function actionIndex()
    {
        $this->view->title = '充值';
        $amount = 1;

        //保存充值记录
        $userCharge = new UserCharge();
        $userCharge->user_id = u()->id;
        $userCharge->trade_no = u()->id . date("YmdHis") . rand(1000, 9999);
        $userCharge->amount = $amount;
        //1支付宝2微信3银行卡
        $userCharge->charge_type = 2;
        //充值状态：1待付款，2成功，-1失败
        $userCharge->charge_state = 1;
        if (!$userCharge->save()) {
            return false;
        }

        return $this->render('pay', compact('amount'));
    }

    //异步支付回调地址
    public function actionNotify()
    {

        //测试订单号
        $trade_no = $_REQUEST["orderid"];

        $userCharge = UserCharge::find()->where('trade_no = :trade_no', [':trade_no' => $trade_no])->one();
        //有这笔订单
        if (!empty($userCharge)) {
        //充值状态：1待付款，2成功，-1失败
            if ($userCharge->charge_state == 1) {
            //找到这个用户
                $user = User::findOne($userCharge->user_id);
            //给用户加钱
                $user->account += $userCharge->amount;
                if ($user->save()) {
                //更新充值状态---成功
                    $userCharge->charge_state = 2;
                }
            }
        //更新充值记录表
            $userCharge->update();
        }
        
    }

    //异步支付回调地址
    public function actionSynotify()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);


        $merchant_id = $data['merchant_id'];//商户号
        $trade_id = $data['trade_id'];//支付平台订单号
        $trade_no = $data['trade_no'];//商户订单号，唯一
        $price = $data['price'];//请求支付金额，元
        $real_price = $data['real_price'];//支付通道接收的实际金额，元
        $pay_type = $data['pay_type'];//支付方式1-支付宝 2-微信
        $good_name = $data['good_name'];//商户名称
        $good_description = $data['good_description'];//商品描述
        $notify_url = $data['notify_url'];//异步通知url
        $return_url = $data['return_url'];//支付结果跳转地址
        $user_id = $data['user_id'];//商户平台中充值用户的唯一标识id
        $user_mobile = $data['user_mobile'];//商户平台中充值用户的手机号码
        $open_time = $data['open_time'];//支付订单发起时间，格式2018-01-01 12:00:00
        $close_time = $data['close_time'];//支付完成时间，格式2018-01-01 12:00:00
        $signature = $data['signature'];//签名
        $sign = $merchant_id . $trade_id . $trade_no . $price . $real_price . $pay_type . $good_name . $good_description . $notify_url . $return_url . $user_id . $user_mobile . $open_time . $close_time . SY_KEY;
		//var_dump($sign);die;
        $sign = md5($sign);
        if ($signature === $sign) {
            $userCharge = UserCharge::find()->where('trade_no = :trade_no', [':trade_no' => $trade_no])->one();
            //有这笔订单
            if (!empty($userCharge)) {
                //充值状态：1待付款，2成功，-1失败
                if ($userCharge->charge_state == 1) {
                    //找到这个用户
                    $user = User::findOne($userCharge->user_id);
                    //给用户加钱
                    $user->account += $userCharge->amount;
                    if ($user->save()) {
                        //更新充值状态---成功
                        $userCharge->charge_state = 2;
                    }
                } else {
                    echo '订单已支付';
                    exit;
                }
                //更新充值记录表
                $userCharge->update();
                $json = '{"code":"0","msg":"ok"}';
                return $json;
            } else {
                echo '订单不存在';
                exit;
            }
        } else {
            echo 'error';
            exit;
        }


    }

    public function actionMcbnotify()
    {
        //http://wsyii.com/pay/mcbnotify?key=
        $getkey = $_REQUEST['key'];//接收参数key
        $tno = $_REQUEST['tno'];//接收参数tno 交易号
        $payno = $_REQUEST['payno'];//接收参数payno 一般是用户名 用户ID
        $money = $_REQUEST['money'];//接收参数money 付款金额
        $sign = $_REQUEST['sign'];//接收参数sign
        $typ = (int)$_REQUEST['typ'];//接收参数typ
        if ($typ == 1) {
            $typname = '手工充值';
        } else if ($typ == 2) {
            $typname = '支付宝充值';
        } else if ($typ == 3) {
            $typname = '财付通充值';
        } else if ($typ == 4) {
            $typname = '手Q充值';
        } else if ($typ == 5) {
            $typname = '微信充值';
        }

        if (!$tno) exit('没有订单号');
        if (!$payno) exit('没有付款说明');
        if (!is_numeric($payno)) exit('付款说明不是数字');
        if ($getkey != MCB_KEY) exit('KEY错误');
        if (strtoupper($sign) != strtoupper(md5($tno . $payno . $money . MCB_MD5KEY))) exit('签名错误');
        //查询数据库 交易号tno是否存在
        $userCharge = UserCharge::find()->where('trade_no = :trade_no', [':trade_no' => $payno])->one();
        if (!empty($userCharge)) {
            //充值状态：1待付款，2成功，-1失败
            if ($userCharge->charge_state == 1) {
                //找到这个用户
                $user = User::findOne($userCharge->user_id);
                //给用户加钱
                $user->account += $userCharge->amount;
                if ($user->save()) {
                    //更新充值状态---成功
                    $userCharge->charge_state = 2;
                }
            }
            //更新充值记录表
            $userCharge->update();
            exit('1');
        }
    }
    public function actionSyreturn()
    {
        echo "<script>alert('充值成功!');location.href='/user/index';</script>";
    }

    public function actionBack_url()
    {
        $myappid = MCB_APPID;//您的APPID
        $appkey = MCB_APPKEY;//您的APPKEY
//***********************
        if (!isset($_REQUEST['appid']) || !isset($_REQUEST['tno']) || !isset($_REQUEST['payno']) || !isset($_REQUEST['money']) || !isset($_REQUEST['typ']) || !isset($_REQUEST['paytime']) || !isset($_REQUEST['sign'])) {
            exit('参数错误');
        }
        $appid = (int)$_REQUEST['appid'];
        $tno = $_REQUEST['tno'];//交易号 支付宝 微信 财付通 的交易号
        $payno = $_REQUEST['payno'];//网站充值的用户名
        $money = $_REQUEST['money'];//付款金额
        $typ = (int)$_REQUEST['typ'];
        $paytime = $_REQUEST['paytime'];
        $sign = $_REQUEST['sign'];
        if (!$appid || !$tno || !$payno || !$money || !$typ || !$paytime || !$sign) {
            exit('参数错误');
        }
        if ($myappid != $appid) exit('appid error');
//sign 校验
        if ($sign != md5($appid . "|" . $appkey . "|" . $tno . "|" . $payno . "|" . $money . "|" . $paytime . "|" . $typ)) {
            exit('签名错误');
        }
//处理用户充值
        if ($typ == 1) {
            $typname = '手工充值';
        } else if ($typ == 2) {
            $typname = '支付宝充值';
        } else if ($typ == 3) {
            $typname = '财付通充值';
        } else if ($typ == 4) {
            $typname = '手Q充值';
        } else if ($typ == 5) {
            $typname = '微信充值';
        }

        if (!$tno) exit('没有订单号');
        if (!$payno) exit('没有付款说明');
        $userCharge = UserCharge::find()->where('trade_no = :trade_no', [':trade_no' => $payno])->one();
        if (!empty($userCharge)) {
            //充值状态：1待付款，2成功，-1失败
            if ($userCharge->charge_state == 1) {
                //找到这个用户
                $user = User::findOne($userCharge->user_id);
                //给用户加钱
                $user->account += $userCharge->amount;
                if ($user->save()) {
                    //更新充值状态---成功
                    $userCharge->charge_state = 2;
                }
            }
            //更新充值记录表
            $userCharge->update();
//            exit('1');
        } else {
            exit('订单号不存在');
        }
        return $userCharge->amount;

    }

    public function actionMcbreturn()
    {
        $amount = $this->actionBack_url();
        echo "<script>alert('充值成功!');location.href='/user/index';</script>";
        //return $this->render('pay', compact('amount'));
    }


    //PaysApi支付回调
    public function actionApnotify()
    {
        $paysapi_id = $_REQUEST["paysapi_id"];
        $orderid = $_REQUEST["orderid"];
        $price = $_REQUEST["price"];
        $realprice = $_REQUEST["realprice"];
        $orderuid = $_REQUEST["orderuid"];
        $key = $_REQUEST["key"];
        //校验传入的参数是否格式正确，略
        $token = "95b152eb7779c6d5b8c03ec2e83ddf0e";
        $temps = md5($orderid . $orderuid . $paysapi_id . $price . $realprice . $token);
        if ($temps != $key) {
            return jsonError("key值不匹配");
        } else {
        //校验key成功，是自己人。执行自己的业务逻辑：加余额，订单付款成功，装备购买成功等等。
            $userCharge = UserCharge::find()->where('trade_no = :trade_no', [':trade_no' => $orderid])->one();
                //有这笔订单
            if (!empty($userCharge)) {
                $tradeAmount = $realprice;
                if ($userCharge->charge_state == UserCharge::CHARGE_STATE_WAIT) {
                    $user = User::findOne($userCharge->user_id);
                    $user->account += $tradeAmount;
                    if ($user->save()) {
                        $userCharge->charge_state = UserCharge::CHARGE_STATE_PASS;
                    }
                }
                if ($userCharge->update()) {
                    return jsonSuccess("success", $userCharge);
                }
            } else {
                return jsonError("该订单不存在");
            }

        }

    }

}
