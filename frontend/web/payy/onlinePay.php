<?php







		$rechargeId ='201709288615311';  //订单号
		$amount ='2';  //金额
		$bankid ='30002';  //银行类型
		$parter = '1735';    //商户id
   $key = 'd2ded0eea5874e6eb5e2a9c7d7552a68';   //商户key
   $callbackurl = "http://www.100gift.cn/payy/Back.php";   //异步通知
   $hrefbackurl = "http://www.100gift.cn/payy/return.php";   //同步通知

   $signStr = "parter=$parter&type=$bankid&value=$amount&orderid=$rechargeId&callbackurl=$callbackurl";

   $sign	= md5($signStr.$key);


			
			   $url="http://pay.1515qp.com/bank/?" . $signStr . "&sign=" .$sign. "&hrefbackurl=".$hrefbackurl;
				header("Location: ".$url); 
						
			
	



?>