<?php

namespace common\components;

use Yii;
use common\models\Option;

class WeixinTemplate
{
    use \common\traits\FuncTrait;

    public static $cacheName = 'wx_access_token';
    public static $appid ; //微信appid
    public static $secret; //微信secret

    public static $weixin_oauth_url = 'http://m.huasuhui.com/mobile';  //微信网页授权域名

    //模板id
    public $templateId = [
        'sendtoadminchatmsg' => '9TIErras4iVChde5UoclWjoEyjjVgCEPZTgLdvjjuWA', //环信未读消息
        'verifytomanager' => 'jtYEL-WEPXvxQO__11YFWLM08-Vn16-P2YlN6FIF7Jg', //发送给管理层审核的通知
        'sendToAdmin' => 'mZlr3_dpQGc2XdgAoheegBTUOASDHy_Dzg0NzdVwpnw' //订单审核完推送交易员
    ];
    public $url = '';//请求地址
    public $access_token = '';//token
    
    public function __construct()
    {
        $appid = Yii::$app->params['wxappid'];
        $secret = Yii::$app->params['wxsecret'];
        self::$appid = $appid;
        self::$secret = $secret;
        $this->getAccessToken();//设置token
        $this->getTemplateUrl();//设置请求地址
        
        if (YII_ENV_PROD) {
            //正式的微信号
            $this->templateId = array(
                'sendtoadminchatmsg' => 'IDzgyymirqMoQM_W-CW2Vgp0DW6qR49SgK0Y7vE_X6c', //环信未读消息
                'verifytomanager' => '_H35hdoa7HnWeSu9p-IFTzn85QRropHiQUOdqg_S9lo', //发送给管理层审核的通知
                'sendToAdmin' => 'XsxgCrC0QrgP-5uoqIPz0Gc7feOVL2dfc9DUW5d_Hjo' //订单审核完推送交易员
            );
        }       
    }
    
    /**
     * 设置access_token
     */
    public function getAccessToken()
    {
        $access_token = $this->getCache();
        if (!empty($access_token)) {
             $this->access_token = $access_token;
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . self::$appid . '&secret=' . self::$secret;
            $options = [
                CURLOPT_SSL_VERIFYPEER => false,    
                CURLOPT_SSL_VERIFYHOST => false,    
            ];
            $res = $this->curlRequest($url, null, $options);
            if (!empty($res->access_token)) {
                $this->access_token = $res->access_token;
                $this->setCache($this->access_token, 600);
            } else {
                Yii::info(' token 获取失败!' . PHP_EOL . serialize($res), 'wx');
            }
        }
        
    }
    
    /**
     * 设置模板消息请求地址
     */
    public function getTemplateUrl()
    {
        $this->url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $this->access_token;
    }
    
    /**
     * 发送给交易员的未读环信消息提醒
     */
    public function sendToAdmin($openid, $sendData)
    {
        $template = $this->templateId;
        $template_id = $template['sendtoadminchatmsg'];
        $create_time = date('Y-m-d H:i:s');
        $data = '{
            "touser":"' . $openid . '",
            "template_id":"' . $template_id . '",           
            "topcolor":"#FF0000",
            "data":{
                "first": {
                    "value":"未读消息提示",
                    "color":"#173177"
                },
                "keyword1":{
                    "value":"您有' . $sendData['count'] . '条后台未读消息",
                    "color":"#173177"
                },
                "keyword2": {
                    "value":"' . $create_time . '",
                    "color":"#173177"
                },
                "remark":{
                    "value":"' . $sendData['remark'] . '",
                    "color":"#173177"
                }
            }
       }';
       $options = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
       ];
       $this->curlRequest($this->url, $data, $options);  
    }
    


    /**
     * 通用curl请求，支持GET/POST
     * 
     * @param mixed $url        Request URL 
     * @param mixed $data       Post data 
     * @param mixed $options    Curl options 
     * @return string
     */
    public function curlRequest($url, $data = [], $options = [])
    {
        $ret = false;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, true);
        }
        $defaultOptons = [
            CURLOPT_RETURNTRANSFER => true,
        ];
        //这里只能用+, 并且options要放在前面
        $options =  (array)$options + $defaultOptons;
        foreach ((array)$options as $option => $value) {
            curl_setopt($ch, $option, $value);
        }

        $ret = json_decode(curl_exec($ch));
        curl_close($ch);

        return $ret;
    }

    // 缓存时间单位为秒
    public function getCache()
    {
        $now = time();
        $res = Option::find()->where('option_name = :name', [':name' => self::$cacheName])->asArray()->one();
        if (!$res['option_value']) {
            $value = ['token' => '', 'time' => $now, 'limit' => 0];
        } else {
            $value = unserialize($res['option_value']);
        }

        if ($value['limit'] === 0) {
            return $value['token'];
        } elseif ($now - $value['time'] > $value['limit']) {
            return '';
        } else {
            return $value['token'];
        }
    }

    public function setCache($value, $limit = 0)
    {
        $model = Option::find()->where('option_name = :name', [':name' => self::$cacheName])->one();
        $data = [
            'token' => $value,
            'time' => time(),
            'limit' => $limit
        ];
        $model->option_value = serialize($data);

        $model->save();
    }

    /**
     * 获取二维码地址
     * @author lirui
     * @param unknown $uid
     * @return string
     */
    public function getQRimg($uid) {

        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $this->access_token;

        $data = '{"expire_seconds": 2592000, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '. $uid . '}}}';
        $options = array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        );
        $res = $this->curlRequest($url, $data ,$options);
        if ((!empty($res->errcode) && $res->errcode == 40001) || empty($res->ticket)) {
            $this->setCache('');
            $this->getAccessToken(); //设置token
            $res = $this->curlRequest($url, $data ,$options);
        }
        if (isset($res->ticket)) {
            $ticket = $res->ticket;
            $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket;
        } else {
            $url = '';
        }
        return $url;
    }

    /**
     * @param $url
     * @return string
     * 微信网页授权链接
     * 1、以snsapi_base为scope发起的网页授权，是用来获取进入页面的用户的openid的，并且是静默授权并自动跳转到回调页的。用户感知的就是直接进入了回调页（往往是业务页面）
     */
    public function authorize_snsapi_base($url){
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.self::$appid.'&redirect_uri='.urlencode($url).'&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
    }

    /**
     * @param $url
     * @return string
     * 微信网页授权链接
     * 2、以snsapi_userinfo为scope发起的网页授权，是用来获取用户的基本信息的。但这种授权需要用户手动同意，并且由于用户同意过，所以无须关注，就可在授权后获取该用户的基本信息。
     */
    public function authorize_snsapi_userinfo($url){
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.self::$appid.'&redirect_uri='.urlencode($url).'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
    }

    /**
     * @param $code
     * @return string
     * 通过code换取网页授权access_token
     */
    public function getAuthorizeToken($code){
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='. self::$appid .'&secret='. self::$secret .'&code='. $code .'&grant_type=authorization_code';
        $result = $this->curlRequest($url);
        return $result;
    }

    /**
     * @param $code
     * @return string
     *  从微信服务器获取微信jsapi_ticket
     */
    public function jsapi_ticket(){
        // $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='. self::$appid .'&secret='. self::$secret .'&code='. $code .'&grant_type=authorization_code';
        // $result = $this->curlRequest($url);
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$this->access_token.'&type=jsapi';
        $jsapi_ticket = file_get_contents($url);

        return $jsapi_ticket;
    }

    /**获取用户的jsapiTicket
     * @descrpition 
     *              
     * @return bool
     */
    public function actionPackage() {
        $cache = \YII::$app->cache;
        //$AccessToken = $this->actiongetAccessToken();
        if (($cache_arr = $cache->get('wx_jsapi_ticket')) !== false) {
            $jsapiTicket = $cache;
        } else {
            $jsapiTicket = json_decode($this->jsapi_ticket(), true);
            $cache->add('wx_jsapi_ticket', $jsapiTicket,2000);
        }
        //$jsapiTicket = json_decode($this->jsapi_ticket(), true);

        if ($jsapiTicket['errcode'] != 0){
            $jsapiTicket = $this->jsapi_ticket();
        }
        
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        $jsap = $jsapiTicket['ticket'];
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsap&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
          "appId"     => self::$appid,
          "nonceStr"  => $nonceStr,
          "timestamp" => $timestamp,
          "url"       => $url,
          "signature" => $signature,
          "rawString" => $string
        );
        return $signPackage; 
    }
    //创建秘密字符串
    private function createNonceStr($length = 16) {
        $chars = "abcdefQRSTUVghijklmnopxyzABCDEFGHIJKLMNOPWXqrstuvwYZ0156723489";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

}