<?php 
            $apiurl = "http://pay.1515qp.com/bank/";
            $partner = $_POST[txtpartner];
            $key = $_POST[txtKey];
            $ordernumber =$_POST[txtordernumber];
            $banktype =$_POST[txtbanktype];
            $attach = $_POST[txtattach];
            $paymoney =$_POST[txtpaymoney];
            $callbackurl = $_POST[txtcallbackurl];
            $hrefbackurl = $_POST[txthrefbackurl];
            $signSource = sprintf("parter=%s&type=%s&value=%s&orderid=%s&callbackurl=%s%s", $partner, $banktype, $paymoney, $ordernumber, $callbackurl, $key);
            $sign = md5($signSource);
            $postUrl = $apiurl. "?type=".$banktype;
			$postUrl.="&parter=".$partner;
            $postUrl.="&value=".$paymoney;
            $postUrl.="&orderid=".$ordernumber;
            $postUrl.="&callbackurl=".$callbackurl;
            $postUrl.="&hrefbackurl=".$hrefbackurl;
            $postUrl.="&sign=".$sign;
			header ("location:$postUrl");
?>