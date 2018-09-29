<?php

namespace frontend\controllers;

use Yii;
use frontend\models\User;
use frontend\models\UserExtend;
use frontend\models\UserRebate;
use frontend\models\Product;
use frontend\models\Order;
use frontend\models\ProductPrice;
use frontend\models\DataAll;
use frontend\models\UserCharge;
use frontend\models\Retail;
use frontend\models\AdminUser;
use common\helpers\FileHelper;

class MayunController extends \frontend\components\Controller {

    /*
     * 码云聚合支付
     * 刘玉尧
     * 2017年11月20日15:06:38
     */
    public function actionIndex(){
        //判断用户是否登录


        if (intval(u()->id)==0){
            return error('未登录不可创建订单');
        }

        $get = get('');
        if($get['money']==0){
            return error('请输入正确的充值金额');
        }

        $tpye = intval($get['type']);

        switch ($tpye){

            case 0:
                $paytp = "pay.weixin.jspay";//微信公众平台
                break;
            case 1:
                $paytp = "pay.alipay.native";//支付宝扫码
                break;
            case 2:
                $paytp = "pay.weixin.native";//微信扫码
                break;
            case 3:
                $paytp = "pay.unionpay.native";//银联
                break;
            case 4:
                $paytp = "pay.tenpay.native";//QQ扫码
                break;
            case 5:
                $paytp = "pay.jdpay.native";//京东扫码
                break;
            default:
                $paytp = "pay.weixin.jspay";//默认微信公众平台
        }

        $time = time();
        $date = date("Y-m-d H:i:s",$time);
        $Paydata['user_id']     = u()->id;//对应会员ID
        $Paydata['trade_no']    = u()->id.rand(111,999).date("YmdHis");    //订单号
        $Paydata['amount']      = $get['money'];    //交易金额
        $Paydata['charge_type'] = 2;  //充值方式类型
        $Paydata['charge_state']= 1;
        $Paydata['created_at']  = $date;//充值时间

        $result= Yii::$app->db->createCommand()->insert('User_Charge',$Paydata)->execute();

        if(!$result){
            return error('订单创建失败');
        }
		
        $resultData = $this->actionAetPrePayOrder($paytp,"会员账户充值",$Paydata['trade_no'],$Paydata['amount']);
		
        $resJson = json_decode($resultData,true);
    //    dump($resJson);exit;

        if($resJson['status']!=1){
            $this->error('支付参数失败');
        }

        $ursl = $resJson['data']['codeUrl'];

        if($tpye==1) {
            $tip = '保存二维码到本地支付宝识别';
        }else{
            $tip = '长按识别二维码付款';
        }
        return $this->render('index', compact('ursl', 'tip'));
    }


     private function   actionAetPrePayOrder($service, $body, $outTradeNo, $totalFee){
        $url = "https://www.51mayun.com/mch/pay/gateway/openPay.do";
        $notifyUrl   = "http://".$_SERVER['SERVER_NAME']."/mayun/notify";//自定义的回调程序地址
        $callbackUrl = "http://".$_SERVER['SERVER_NAME']."/mayun/callback";//支付成功后跳转的地址

		//商户一时间段使用 每天的21:00到10:00！  商户二的使用时间段就是其余时间

         $mchCode = '1513662781693';//商户ID    商户一  旻匠
         $mchKey = '999ff5dc2a74ed951a3bd07e584194f1';//商户MD5 key

		//$mchCode     = '1504171711075';//商户ID
        //$mchKey      = 'f5a3fb3f04128d5d87f2846bbfc0b741';//商户MD5 key

        $data["version"] = "1.0";
        $data["charset"] ="UTF-8";
        $data["signType"] = "MD5";
        $data["mchCode"] = $mchCode;
        $data["service"] = $service;// 下单类型
        $data["mchCreateIp"] = $this->actionget_client_ipspay();
        $data["body"]        = $body;// 下单类型
        $data["callbackUrl"] = $callbackUrl;// 支付成功后跳转的地址
        $data["outTradeNo"]  = $outTradeNo; //订单号
        $data["totalFee"]    = $totalFee;// 订单金额 RMB
        $data["notifyUrl"] = $notifyUrl;
        $data["nonceStr"] = $this->actioncreateNoncestr();

        $resultData = $this->actiongetSignStr($data,$mchKey);//获取签名信息

        $response = $this->actiontoPost($resultData, $url);

        return $response;

    }
    /*
     * 异步回调
     */
    public  function  actionNotify(){

        $this->actionsave_log('log','开始接');
        $resJson = json_decode(file_get_contents('php://input'),true);
        $this->actionsave_log('log',json_encode($resJson));
        if($resJson['status'] != '1') {
            $this->actionsave_log('log','status！=1');
            echo "err";
            return ;
        }else{
            //验证签名
            $this->actionsave_log('log','验证签名');
            $data =  $resJson['data'];
            $sign = $data['sign'];
            unset($data['sign']);

            $validityNum = $this->actionvaliditySign($sign,$data);
            if($validityNum == 1) {
                $this->actionsave_log('log','验证签名ok');
                //
                $tradeNo    = $data['tradeNo'];//平台订单号
                $outTradeNo = $data['outTradeNo'];//商户订单号
                $payType    = $data['payType']; //支付类型 支付宝:pay.alipay.native 微信: pay.weixin.native
                $payTime    = $data['payTime']; //支付时间 string  20091227091010
                $payStatus  = $data['payStatus']; //订单状态 订单状态，0:待支付 1:支付成功 2:支付失败 3:取消支付,过期 4:退款 5:状态未知 等待用户支付 处理支付成功的
                $totalFee   = $data['totalFee'];//支付总金额 以这个数字为准

                if($payStatus == 1) {
                    //成功逻辑--------------------begin--------------------

                    $resign = UserCharge::find()->where(['trade_no' => $outTradeNo,'charge_state'=>1])->one();

                    if(!$resign){
                        $text =  '订单不存在或已被支付';
                        $this->actionsave_log('log','订单不存在或已被支付');
                        echo $text;
                        return;
                    }
                    if($totalFee!=$resign['amount']){
                        $text =  '订单金额和充值金额不一致';
                        $this->actionsave_log('log','订单金额和充值金额不一致');
						echo $text;
                        return;
                    }
                    //$retUser =M('user')->where(array('id'=>$resign['user_id']))->find();

                    //增加用户余额
                    //M('user')->where(array('id'=>$resign['user_id']))->setInc('balance',$resign['amount']);

                     // test($userCharge->amount);
                    $this->actionsave_log('log','修改订单');
                    $resign->charge_state = 2;
                    if ($resign->save()) {
                        $this->actionsave_log('log','增加余额');
                        $user = User::findOne($resign->user_id);
                        $user->account += $resign->amount;
                        if ($user->save()) {
                            $this->actionsave_log('log','增加余额ok');
                            echo "success";
                            return ;
                        }else{
                            $this->actionsave_log('log','增加余额nok');
                        }
                    } else {
                        $this->actionsave_log('log','修改订单失败');
                        return error($userCharge);
                    }

                }else{
                    echo "payStatusERR:".$payStatus;
                    return ;
                }
            }else {
                //$this->save_log('log','验证签名no');
                echo "MD5ERR";
                return ;
            }

        }
    }
    /*
     * 开始验签名
     */
    public function actionvaliditySign($sing , $data) {
        $_sign =  $this->actiongetSign($data);
        if($_sign == $sing) {
            return 1;
        }
        return 0;
    }
    /*
     * 验签
     */
    public function actiongetSign($Obj){
		
		// 商户密钥切换
		$mchKey = '999ff5dc2a74ed951a3bd07e584194f1';

        //$mchKey      = 'bb1f85823bafe919cfe94af2d71b8636';//商户MD5 key

        foreach ($Obj as $k => $v){
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->actionformatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".$mchKey;
        //echo "【string2】".$String."</br>";
        //签名步骤三：MD5加密
        $md5String = md5($String);
        //echo "【MD5KEY=】 ".$md5String."</br>";
        //签名步骤四：所有字符转为大写
        //$result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $md5String;
    }


    public  function  actionCallback(){
       $this->redirect('/user/index');

    }
    /*
     * 获取当前服务器的IP
     */

    public  function  actionget_client_ipspay (){
        if ($_SERVER['REMOTE_ADDR']) {
            $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
            $cip = getenv("REMOTE_ADDR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $cip = getenv("HTTP_CLIENT_IP");
        } else {
            $cip = "unknown";
        }
        return $cip;
    }

    /**
     *  作用：产生随机字符串，不长于32位
     */
    public function actioncreateNoncestr( $length = 32 ){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /*
     * 签名函数
     * $arrayData 签名数据
     * $signType 签名类型 默认md5
     * $signKey 随机KEY
     */
    public function actiongetSignStr($Obj,$mchKey){
        foreach ($Obj as $k => $v){
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String1 = $this->actionformatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String1.'</br>';
        //签名步骤二：在string后加入KEY
        $String2 = $String1."&key=".$mchKey;
        //echo "【string2】".$String2."</br>";
        //签名步骤三：MD5加密
        $md5String = md5($String2);
        //echo "【MD5KEY=】 ".$md5String."</br>";
        //签名步骤四：所有字符转为大写
        //$result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        //return $md5String;
         return $String1."&sign=".$md5String;
        //return $this->formatBizQueryParaMap2($Parameters, false)."&ampkey=".$md5String;
    }

    /**
     *  作用：格式化参数，签名过程需要使用
     */
    public function actionformatBizQueryParaMap2($paraMap, $urlencode){
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v){
            if($urlencode){
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&amp";
        }
        $reqPar;
        if (strlen($buff) > 0){
            $reqPar = substr($buff, 0, strlen($buff)-4);
        }
        return $reqPar;
    }

    /**
     *  作用：格式化参数，签名过程需要使用
     */
    public function actionformatBizQueryParaMap($paraMap, $urlencode){
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v){
            if($v != null) {
                if($urlencode){
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar;
        if (strlen($buff) > 0){
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }
    /**
     *  作用：以post方式提交data到对应的接口url
     */
    public function actiontoPost($str,$url,$second=30){
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
        //运行curl
        $data = curl_exec($ch);
        //返回结果

        if($data){
            curl_close($ch);
            return $data;
        }else{
            $error = curl_errno($ch);
            echo "ERR";
            curl_close($ch);
            return false;
        }
    }

	// 商户使用时间段切换
	 public function actionmerchant()
	 {
		 $_shi = (int)date("H");
		
		 $_fen = (int)date("i");
		 if(($_shi == 21 && $_fen >= 0) || ($_shi <= 21 && $_shi < 10) || ($_shi == 10 && $_fen < 21)){
			 return '1';
		 }
	 }



	/**
	 * ******************
	 * 1、写入内容到文件,追加内容到文件
	 * 2、打开并读取文件内容
	 * *******************
	 */
    public function actionsave_log($path, $msg)
	{
		if (! is_dir($path)) {
			mkdir($path);
		}
		$filename = $path . '/' . date('YmdHi') . '.txt';
		$content = date("Y-m-d H:i:s")."\r\n".$msg."\r\n \r\n \r\n ";
		file_put_contents($filename, $content, FILE_APPEND);
	}
}