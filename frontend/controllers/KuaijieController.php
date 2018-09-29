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
use frontend\models\BankCard;
use frontend\models\Retail;
use frontend\models\AdminUser;
use common\helpers\FileHelper;

class KuaijieController extends \frontend\components\Controller {
	
    /*
     * 银联快捷支付
     * 刘玉尧
     * 2017年12月29日11:25:31
	 * 吐槽-这狗比支付公司就是S吊一个-
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
		$bankCard = BankCard::find()->where(['user_id' => u()->id])->one();
        if (!$bankCard) {
           return $this->redirect(['user/bankCard']);
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
		
		//报备参数
	    $param = array(
			'mch_id'=>'pw-9e3f-8a80110d1b96',			//商户号
			'bank_name'=>$bankCard['bank_name'],   		//银行名称
			'account_no'=>$bankCard['bank_card'],		//银行帐号
			'account_name'=>$bankCard['bank_user'],		//银行账户
			'phone'=>$bankCard['bank_mobile'],			//手机号
			'id_no'=>$bankCard['id_card'],				//身份证号
			'cardType'=>'01',				 		    //卡类型  01=借记卡、00=贷记卡
			'expdate'=>'0',						        //到期日期 01时传0、00时传到期日期
			'cvv2'=>'0',							    //cvv2编码 01时传0、00时传cvv2编码 
			'out_trade_no'=>$Paydata['trade_no'],			    //订单号
			'amount'=>(string)$Paydata['amount']*100,							//订单金额  分为单位
			'body'=>'会员充值',							//产品说明
			'notify_url'=>("http://".$_SERVER['SERVER_NAME']."/kuaijie/notify"),// 填写你自己的回调地址
		);
		
		$param['sign'] = $this->actionMakeSignature($param, '3ae33625a49ecf2ce88848188a0fead592976d4b');
		$param['tdtype'] = 'K2';
		
		$url_param = $this->actionArrayToKeyValueString($param);
		$backdata = file_get_contents('http://www.nbdtd.com/api/quick/order?'.$url_param);
		$retSign  = json_decode($backdata,true);
		$retSign['money']  = $Paydata['amount'];
		if($retSign['respCode']=='00000'){
			return $this->render('kuaijie', compact('retSign', $retSign));
		}else{
			return error($retSign['respMsg']);
		}
		
    }
	
	/*
    * 生成签名，$args为请求参数，$key为私钥
    */
	function actionMakeSignature($args, $key){
		if(isset($args['sign'])) {
			$oldSign = $args['sign'];
			unset($args['sign']);
		} else {
			$oldSign = '';
		}
        ksort($args);
        $requestString = '';
        foreach($args as $k => $v) {
            $requestString .= $k . '='.($v);
            $requestString .= '&';
        }
        $requestString = substr($requestString,0,strlen($requestString)-1);
        $newSign = md5( $requestString."&key=".$key);
        return $newSign;
    }
	
	/*
    * 生成签名，签名转换
    */
	function actionArrayToKeyValueString($param){
		$str = '';
		foreach($param as $key => $value) {
			$str = $str . $key .'=' . $value . '&';
		}
		return $str;
	}
	/* 第二步 确认付款*/
	function actionConfim(){
		//报备参数
		$out_trade_no = $_POST['out_trade_no'];
		$msmCode = $_POST['msmCode'];
		if($msmCode==''){
			return error('请输入正确的验证码');
		}
	    $param = array(
			'mch_id'=>'pw-9e3f-8a80110d1b96',			//商户号
			'out_trade_no'=>$out_trade_no,			    //订单号
			'smscode'=>$msmCode,						//第一次通过sign接口获取验证码填写, 以后就传0
		);
		// 签名
		$param['sign'] = $this->actionMakeSignature($param, '3ae33625a49ecf2ce88848188a0fead592976d4b');
		$param['tdtype'] = 'K2';
		$url_param = $this->actionArrayToKeyValueString($param);
		$backdata = file_get_contents('http://www.nbdtd.com/api/quick/confirm?'.$url_param);
		$retSign  = json_decode($backdata,true);
		if($retSign['respCode']=='00000'){
			return success($retSign['respMsg']);
		}else{
			return error($retSign['respMsg']);
		}
	}
	
	
    /*
     * 异步回调
     */
    public  function  actionNotify(){

        $this->actionsave_log('log','开始接');
		
        $backdata = file_get_contents("php://input");
		$this->actionsave_log('log',$backdata);
		if($backdata==''){
			$this->actionsave_log('log','请求值无数据');
			echo '{"status":false}';
			return ;
		}
		$parr 		   = explode('&', $backdata);
		$parr0 	       = explode('=', $parr[0]);
		$out_trade_no  = $parr0[1];
		$parr1         = explode('=', $parr[1]);
		$out_channel_no= $parr1[1];
		$parr2         = explode('=', $parr[2]);
		$respCode      = $parr2[1];
		$parr4         = explode('=', $parr[4]);
		$sign          = $parr4[1];
		if($respCode == '00000') {
			$param = array(
				'out_trade_no'=> $out_trade_no,
				'out_channel_no'=> $out_channel_no,
			);
			if($sign == $this->actionMakeSignature($param, '3ae33625a49ecf2ce88848188a0fead592976d4b')){
				// 业务代码写到这里
				$this->actionsave_log('log','验证签名');
				
				$resign = UserCharge::find()->where(['trade_no' => $out_trade_no,'charge_state'=>1])->one();

				if(!$resign){
					$this->actionsave_log('log','订单不存在或已被支付');
					echo '订单不存在或已被支付';
					return ;
				}
				/* SB 回调没金额 直接读库 入库1万充值一块bug的伏笔已埋下！！！
				if($totalFee!=$resign['amount']){
					$text =  '订单金额和充值金额不一致';
					$this->actionsave_log('log','订单金额和充值金额不一致');
					echo $text;
					return;
				}
				*/
				$this->actionsave_log('log','修改订单');
				$resign->charge_state = 2;
				if ($resign->save()) {
					$this->actionsave_log('log','增加余额');
					$user = User::findOne($resign->user_id);
					$user->account += $resign->amount;
					if ($user->save()) {
						$this->actionsave_log('log','增加余额ok');
						echo '{"status":true}';
						return ;
					}else{
						$this->actionsave_log('log','增加余额nok');
						echo '{"status":false}';
						return ;
					}
				} else {
					$this->actionsave_log('log','修改订单失败');
					echo '{"status":false}';
					return ;
				}
				echo '{"status":true}';
				return ;
			} else {
				$this->actionsave_log('log','签名验证失败');
				echo '{"status":false}';
				return ;
			}		
		}else {
			$this->actionsave_log('log','支付失败');
			echo '{"status":false}';
			return ;
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