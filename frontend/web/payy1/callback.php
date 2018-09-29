<?php 
            $partner = "1735";//商户ID
            $Key = "d2ded0eea5874e6eb5e2a9c7d7552a68";//商户KEY
            $orderstatus = $_GET["opstate"];
            $ordernumber = $_GET["orderid"];
            $paymoney = $_GET["ovalue"];
            $sign = $_GET["sign"];
			
			
			
			signu = asp_md5("orderid="&orderid&"&opstate="&opstate&"&ovalue="&ovalue&userkey)
            $signSource = sprintf("orderid=%s&opstate=%s&ovalue=%s%s", $ordernumber, $orderstatus, $paymoney, $Key); 
            if ($sign == md5($signSource))//签名正确
            {
                //此处作逻辑处理
            }
			echo('ok');exit;

?>