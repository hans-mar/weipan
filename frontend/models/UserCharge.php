<?php

namespace frontend\models;

use Yii;

class UserCharge extends \common\models\UserCharge
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            // [['field1', 'field2'], 'required', 'message' => '{attribute} is required'],
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

    //易支付银行卡绑定
	//SB 写法 放弃掉
    public static function epayBankCard($bankCard)
    {	 return true;
        // test($bankCard->bank_name);
        $data['ORDER_ID'] = u()->id . date("YmdHis");
        $data['ORDER_TIME'] = date("YmdHis");
        $data['USER_TYPE'] = '02';
        $data['USER_ID'] = EXCHANGE_ID;
        $data['SIGN_TYPE'] = '03';
        $data['BUS_CODE'] = '1011';
        $data['CHECK_TYPE'] = '01';
        $data['ACCT_NO'] = $bankCard->bank_card;  // 卡号
        $data['PHONE_NO'] = $bankCard->bank_mobile; //  手机号
        $data['ID_NO'] = $bankCard->id_card;

        $string = '';
        foreach($data as $key => $v) {
            $string .= "{$key}={$v}&";
        }
        $signSource = $string . EXCHANGE_MDKEY;
        // tes($signSource);
        $mdStr = strtoupper(md5($signSource)); //加密算法第一步大写
        $data['SIGN'] = strtoupper(substr(md5($mdStr . EXCHANGE_MDKEY), 8, 16)); //16位的md5
        $data['NAME'] = $bankCard->bank_user; // 姓名
        $value = '';
        foreach($data as $key => $v) {
            $value .= "{$key}={$v}&";
        }
        $value = substr($value, 0, strlen($value)-1);
        // tes($data, $value);
        // $url = 'http://163.177.40.37:8888/NPS-API/controller/pay';
        $url = 'http://npspay.yiyoupay.net/NPS-API/controller/pay';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $value);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        // test($result);
        $str = "<RESP_CODE>0000</RESP_CODE>";
        if(strpos($result,$str)) {
            return true;
        }else {
            return false;
        }
    }

    //云托付
    public static function payYtfchange($amount, $pay_type = "1004")
    {
        //保存充值记录
        $userCharge = new UserCharge();
        $userCharge->user_id = u()->id;
        $userCharge->trade_no = u()->id . date("YmdHis") . rand(1000, 9999);
        $userCharge->amount = $amount;
        $userCharge->charge_type = UserCharge::CHARGE_TYPE_BANKWECHART;
        if($pay_type == '992') {
            $userCharge->charge_type = UserCharge::CHARGE_TYPE_ALIPAY;
            // $amount = 1;
        }
        $userCharge->charge_state = UserCharge::CHARGE_STATE_WAIT;
        if (!$userCharge->save()) {
            return false;
        }
        $url = 'http://pay.yuntuofu.cc/Bank/';
        $data['parter'] = EXCHANGE_ID;
        $data['type'] = $pay_type;
        $data['value'] = $amount;
        $data['orderid'] = $userCharge->trade_no;
        $data['callbackurl'] = url(['site/tynotify'], true);;
        $string = '';
        foreach($data as $key => $v) {
            $string .= "{$key}={$v}&";
        }
        $data['url'] = trim($string, '&') . EXCHANGE_MDKEY;
        $sign = md5($data['url']); 
        $data['sign'] = $sign;
        $data['hrefbackurl'] = url(['site/index'], true);
        return $data;
    }

    //第三方支付 银联支付
    public static function payExtend($amount, $user_id)
    {
        //保存充值记录
        $UserCharge = new UserCharge();
        $UserCharge->user_id = $user_id;
        $UserCharge->trade_no = $user_id . date("YmdHis");
        $UserCharge->amount = $amount;
        $UserCharge->charge_type = UserCharge::CHARGE_TYPE_HUAN;
        $UserCharge->charge_state = UserCharge::CHARGE_STATE_WAIT;
    
        if (!$UserCharge->save()) {
            return false;
        }
        if (0 && System::isMobile()) {
            $url = 'https://mobilegw.ips.com.cn/psfp-mgw/paymenth5.do';
        } else {
            $url = 'https://newpay.ips.com.cn/psfp-entry/gateway/payment.do';
        }
        $MerCode = HX_ID;
        $Account = HX_TID;
        $mercert = HX_MERCERT;
        $MerBillNo = $UserCharge->trade_no;
        $Amount = YII_DEBUG ? '0.01' : $UserCharge->amount . '.00';
        $Date = date('Ymd');
        $GatewayType = '01'; //借记卡：01，信用卡02，IPS账户支付03
        $Merchanturl = WEB_DOMAIN;
        $ServerUrl = WEB_DOMAIN . '/site/notify';// 支付成功回调
        $GoodsName = config('web_name') . '_用户充值';
        $MsgId = 'm'. $MerBillNo;
        $ReqDate = date('Ymdhis');

        $ips = '<Ips><GateWayReq>';
        $body = "<body><MerBillNo>{$MerBillNo}</MerBillNo><Amount>{$Amount}</Amount><Date>{$Date}</Date><CurrencyType>156</CurrencyType ><GatewayType>{$GatewayType}</GatewayType><Lang>GB</Lang><Merchanturl>{$Merchanturl}</Merchanturl><FailUrl></FailUrl><Attach></Attach><OrderEncodeType>5</OrderEncodeType><RetEncodeType>17</RetEncodeType><RetType>1</RetType><ServerUrl>{$ServerUrl}</ServerUrl><BillEXP>1</BillEXP><GoodsName>{$GoodsName}</GoodsName><IsCredit>0</IsCredit><BankCode></BankCode><ProductType>0</ProductType></body>";
        $Signature = md5($body . $MerCode . $mercert);
        $head = "<head><Version>v1.0.0</Version><MerCode>{$MerCode}</MerCode><MerName></MerName><Account>{$Account}</Account><MsgId>{$MsgId}</MsgId><ReqDate>{$ReqDate}</ReqDate><Signature>{$Signature}</Signature></head>";
        $ips .= $head;
        $ips .= $body;
        $ips .= '</GateWayReq></Ips>';
        return ['url' => $url, 'content' => $ips];
        // return $this->render('pay', compact('webAction', 'ips'));
    }
    // 微信支付
    public static function payHxWxpay($amount, $userId)
    {

        //保存充值记录
        $userCharge = new UserCharge(); 
        $userCharge->user_id = $userId;
        $userCharge->trade_no = $userId . date("YmdHis") . rand(1000, 9999);
        $userCharge->amount = $amount;
        $userCharge->charge_type = UserCharge::CHARGE_TYPE_HUAN;
        $userCharge->charge_state = UserCharge::CHARGE_STATE_WAIT;
        if (!$userCharge->save()) {
            return false;
        }  
        $pVersion = 'v1.0.0';//版本号
        $pMerCode = HX_ID;
        $pAccount = HX_TID;
        $pMerCert = HX_MERCERT;
        $pMerName = 'pay';//商户名
        $pMsgId = "msg" . rand(1000, 9999);//消息编号
        $pReqDate = date("Ymdhis");//商户请求时间
        $pMerBillNo = $userCharge->trade_no;//商户订单号
        $pGoodsName = "recharge";//商品名称
        $pGoodsCount = "";
        $pOrdAmt = $userCharge->amount;//订单金额 
        // $pOrdAmt = 0.01;
        $pOrdTime =date("Y-m-d H:i:s");

        $pMerchantUrl = WEB_DOMAIN;
        $pServerUrl = WEB_DOMAIN . '/site/hx-weixin';
        // $pServerUrl = 'http://pay.szsqldjhkjb.top/site/notify';// 支付成功回调
        $pBillEXP="";
        $pReachBy="";
        $pReachAddress="";
        $pCurrencyType="156";
        $pAttach = '用户充值';
        $pRetEncodeType="17";

        $strbodyxml= "<body>"
              ."<MerBillno>".$pMerBillNo."</MerBillno>"
              ."<GoodsInfo>"
              ."<GoodsName>".$pGoodsName."</GoodsName>"
              ."<GoodsCount >".$pGoodsCount."</GoodsCount>"
              ."</GoodsInfo>"
              ."<OrdAmt>".$pOrdAmt."</OrdAmt>"
              ."<OrdTime>".$pOrdTime."</OrdTime>"
              ."<MerchantUrl>".$pMerchantUrl."</MerchantUrl>"
              ."<ServerUrl>".$pServerUrl."</ServerUrl>"
              ."<BillEXP>".$pBillEXP."</BillEXP>"
              ."<ReachBy>".$pReachBy."</ReachBy>"
              ."<ReachAddress>".$pReachAddress."</ReachAddress>"
              ."<CurrencyType>".$pCurrencyType."</CurrencyType>"
              ."<Attach>".$pAttach."</Attach>"
              ."<RetEncodeType>".$pRetEncodeType."</RetEncodeType>"
              ."</body>";
        $Sign = $strbodyxml . $pMerCode . $pMerCert;//签名明文

        $pSignature = md5($strbodyxml.$pMerCode.$pMerCert);//数字签名 
        //请求报文的消息头
        $strheaderxml= "<head>"
               ."<Version>".$pVersion."</Version>"
               ."<MerCode>".$pMerCode."</MerCode>"
               ."<MerName>".$pMerName."</MerName>"
               ."<Account>".$pAccount."</Account>"
               ."<MsgId>".$pMsgId."</MsgId>"
               ."<ReqDate>".$pReqDate."</ReqDate>"
               ."<Signature>".$pSignature."</Signature>"
            ."</head>";

        //提交给网关的报文
        $strsubmitxml =  "<Ips>"
            ."<WxPayReq>"
            .$strheaderxml
            .$strbodyxml
          ."</WxPayReq>"
          ."</Ips>";
          
        $payLinks= '<form style="text-align:center;" action="https://thumbpay.e-years.com/psfp-webscan/onlinePay.do" target="_self" style="margin:0px;padding:0px" method="post" name="ips" >';
        $payLinks  .= "<input type='hidden' name='wxPayReq' value='$strsubmitxml' />";
        $payLinks .= "<input class='btn' type='submit' value='确认支付'></form><script>document.ips2.submit();</script>";
        return ['userCharge' => $userCharge, 'payLinks' => $payLinks];
    }

    //中云第三方支付 ShaoBeiZfb
    public static function payExchange($amount, $acquirer_type = 'WXZF', $tongdao = 'WftWx')
    {
        //保存充值记录
        $userCharge = new UserCharge();
        $userCharge->user_id = u()->id;
        $userCharge->trade_no = u()->id . date("YmdHis") . rand(1000, 9999);
        $userCharge->amount = $amount;
        $userCharge->charge_state = self::CHARGE_STATE_WAIT;
        if ($acquirer_type == 'alipay') {
            $userCharge->charge_type = self::CHARGE_TYPE_ALIPAY;
        }
        if (!$userCharge->save()) {
            return false;
        }
        // test(u()->id);
        // 微信、支付宝交易
        $url = 'http://zy.cnzypay.com/Pay_Index.html';

        $data['pay_memberid'] = ZYPAY_ID; //商户id
        $data['pay_orderid'] = $userCharge->trade_no;
        $data['pay_amount'] = $amount;
        $data['pay_applydate'] = self::$time; //请求时间
        $data['pay_bankcode'] = $acquirer_type; //银行编号
        $data['pay_notifyurl'] = url(['site/notify'], true); //异步回调地址  融智付异步商户url
        $data['pay_callbackurl'] = url(['site/index'], true); //页面返回地址
        // 商户id、应用id、商户订单号、订单金额、加密key
        $string = '';
        ksort($data);
        reset($data);
        foreach($data as $key => $v) {
            $string .= "{$key}=>{$v}&";
        }
        $string .= "key=" . ZYPAY_KEY;
        $data['tongdao'] = $tongdao;
        $data['pay_md5sign'] = strtoupper(md5($string));
        if ($tongdao == 'Gopaywap') {
            $str = '<form id="Form1" name="Form1" method="post" action="' . $url . '">';
            foreach ($data as $key => $val) {
                $str = $str . '<input type="hidden" name="' . $key . '" value="' . $val . '">';
            }
            $str = $str . '<input type="hidden" value="提交">';
            $str = $str . '</form>';
            $str = $str . '<script>';
            $str = $str . 'document.Form1.submit();';
            $str = $str . '</script>';
            return $str;
        }
        $result = httpRequest($url, $data);
        preg_match('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $result, $match);
        if (isset($match[2])) {
            return 'http://zy.cnzypay.com/' . $match[2];
        }
        return false;
    }

    public static function mcb($amount,$pay_type){
        //保存充值记录
        $userCharge = new UserCharge();
        $userCharge->user_id = u()->id;
        $userCharge->trade_no = u()->id . date("YmdHis") . rand(1000, 9999);
        $userCharge->amount = $amount;
        if($pay_type == 'w') {
            $userCharge->charge_type = 5;//微信支付
            // $amount = 1;
        }else{
            $userCharge->charge_type = 2;//支付宝支付
        }

        $userCharge->charge_state = UserCharge::CHARGE_STATE_WAIT;
		//var_dump($userCharge->save());die
        if (!$userCharge->save()) {
            return false;
        }

        $apiurl = MCB_PAY_URL;// "http://pay1.68uw.cn/pay/pay.php";
        $paymoney = $userCharge->amount; //金额
        $ordernumber = $userCharge->trade_no; //订单号
        $callbackurl = MCB_URL_RE; //回调地址
        if(!strstr($callbackurl,"http://")&&!strstr($callbackurl,"https://")){
            if(substr($callbackurl,0,1)=='/'){
                $callbackurl='http://'.$_SERVER['HTTP_HOST'].$callbackurl;
            }else{
                $callbackurl='http://'.$_SERVER['HTTP_HOST'].'/'.$callbackurl;
            }
        }
        $postUrl = $apiurl. "?appid=".MCB_APPID;
        $postUrl.="&payno=". $userCharge->trade_no;
        $postUrl.="&money=".$paymoney;
        $postUrl.="&typ=".$userCharge->charge_type;
        $postUrl.="&back_url=".urlencode($callbackurl);
        header ("location:$postUrl");
        exit('1');
    }


    public static function sy($amount,$pay_type){
        //保存充值记录
        $userCharge = new UserCharge();
        $userCharge->user_id = u()->id;
        $userCharge->trade_no = u()->id . date("YmdHis") . rand(1000, 9999);
        $userCharge->amount = $amount;
        $userCharge->charge_type = 1;//支付宝支付
        $userCharge->charge_state = UserCharge::CHARGE_STATE_WAIT;
        //var_dump($userCharge->save());die
        if (!$userCharge->save()) {
            return false;
        }
        $data = [
            'merchant_id'=>SY_ID,
            'trade_no'=>$userCharge->trade_no,
            'price'=>$userCharge->amount,
            'pay_type'=>'1',
            'good_name'=>'',
            'good_description'=>'',
            'notify_url'=>SY_URL_NO,
            'return_url'=>SY_URL_RE,
            'user_id'=>(string)$userCharge->user_id,
            'user_mobile'=>''
        ];
        $sign = $data['merchant_id'].$data['trade_no'].$data['price'].$data['pay_type'].$data['good_name'].$data['good_description'].$data['notify_url'].$data['return_url'].$data['user_id'].$data['user_mobile'].SY_KEY;
        $sign = md5($sign);
        $data['signature'] = $sign;

        $url = SY_PAY_URL;
        $jsonStr = json_encode($data);
        list($returnCode, $returnContent) = self::http_post_json($url, $jsonStr);
        $json = json_decode($returnContent,true);
        if($json['code']==0) {
           $postUrl = $json['qr_code'];
           header("location:$postUrl");
           exit();
       }else{
           var_dump($returnContent);die;
       }
        //var_dump($json['qr_code']);die;
    }
    public static function http_post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array($httpCode, $response);
    }


	
	    //j云支付
    public static function payRxchange($amount, $pay_type = "1004")
    {
        //保存充值记录
        $userCharge = new UserCharge();
        $userCharge->user_id = u()->id;
        $userCharge->trade_no = u()->id . date("YmdHis") . rand(1000, 9999);
        $userCharge->amount = $amount;
        $userCharge->charge_type = UserCharge::CHARGE_TYPE_BANKWECHART;
        if($pay_type == '30004') {
            $userCharge->charge_type = UserCharge::CHARGE_TYPE_ALIPAY;
            // $amount = 1;
        }
        $userCharge->charge_state = UserCharge::CHARGE_STATE_WAIT;
        if (!$userCharge->save()) {
            return false;
        }
        $data['parter'] = EXCHANGE_ID;
        $data['bank'] = (int)$pay_type;
        $data['value'] = $amount;
        $data['orderid'] = $userCharge->trade_no;
        $data['callbackurl'] = url(['site/tynotify'], true);
        $string = '';
        foreach($data as $key => $v) {
            $string .= "{$key}={$v}&";
        }
        $data['url'] = trim($string, '&') . EXCHANGE_MDKEY;
        $sign = md5($data['url']); 
		$data['urll'] = 'http://api.ecoopay.com/Bank/index.aspx';
        $data['sign'] = $sign;
        $data['hrefbackurl'] = url(['site/index'], true);
        return $data;
    } 


    // PaysApi支付
    public static function PaysApi($pays)
    {
        //保存充值记录
        $userCharge = new UserCharge();
        $userCharge->user_id = u()->id;
        $userCharge->trade_no = u()->id . date("YmdHis") . rand(1000, 9999);
        $userCharge->amount = $pays['price'];
        $userCharge->charge_type = $pays['istype'];//支付宝支付
        $userCharge->charge_state = UserCharge::CHARGE_STATE_WAIT;
        if (!$userCharge->save()) {
            return false;
        }
        
        //从网页传入price:支付价格， istype:支付渠道：1-支付宝；2-微信支付
        $price = $pays["price"];
        $istype = $userCharge->charge_type;

        $orderuid = $userCharge->user_id;       //此处传入您网站用户的用户名，方便在paysapi后台查看是谁付的款，强烈建议加上。可忽略。

    //校验传入的表单，确保价格为正常价格（整数，1位小数，2位小数都可以），支付渠道只能是1或者2，orderuid长度不要超过33个中英文字。

    //此处就在您服务器生成新订单，并把创建的订单号传入到下面的orderid中。
        $goodsname = '充值';
        $orderid = $userCharge->trade_no;    //每次有任何参数变化，订单号就变一个吧。
        // $uid = "f75b9e15f971cc3dbcaf0b44";//"此处填写PaysApi的uid";
        $uid = "412b0f0c86806531659cd78f";
        // $token = "95b152eb7779c6d5b8c03ec2e83ddf0e";//"此处填写PaysApi的Token";
        $token = "cd565a1b3400795dbc4bc6f1b9089f21";
        // $return_url = 'http://'.$_SERVER['SERVER_NAME'].'/site/index';  //跳转网址
        // $notify_url = 'http://'.$_SERVER['SERVER_NAME'].'/pay/notify';  //回调网址
        $notify_url = url(['pay/notify'], true); //异步回调地址  
        $return_url = url(['site/index'], true); //页面返回地址
        // dump($notify_url);exit;
        $key = md5($goodsname . $istype . $notify_url . $orderid . $orderuid . $price . $return_url . $token . $uid);
    //经常遇到有研发问为啥key值返回错误，大多数原因：1.参数的排列顺序不对；2.上面的参数少传了，但是这里的key值又带进去计算了，导致服务端key算出来和你的不一样。

        $returndata['goodsname'] = $goodsname;
        $returndata['istype'] = $istype;
        $returndata['key'] = $key;
        $returndata['notify_url'] = $notify_url;
        $returndata['orderid'] = $orderid;
        $returndata['orderuid'] = $orderuid;
        $returndata['price'] = $price;
        $returndata['return_url'] = $return_url;
        $returndata['uid'] = $uid;
        // return json_encode($returndata);
        echo jsonSuccess("OK", $returndata, "");

    }


}

        