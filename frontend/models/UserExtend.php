<?php

namespace frontend\models;

use Yii;

class UserExtend extends \common\models\UserExtend
{
    public $verifyCode;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['verifyCode'], 'required', 'on' => ['register']],
            // 短信验证码
            [['verifyCode'], 'verifyCode'],
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'register' => ['mobile', 'verifyCode', 'coding', 'realname'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'verifyCode' => '短信验证码',
        ]);
    }

    public function verifyCode()
    {
        if ($this->verifyCode != session('verifyCode')) {
            $this->addError('verifyCode', '短信验证码不正确');
        }
    }

    /*
     * 获得经纪人二维码图片
     * 
     *   http请求方式: POST
     * URL: https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKENPOST数据格式：json
     * POST数据例子：{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
     * 或者也可以使用以下POST数据创建字符串形式的二维码参数：
     * {"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}
     * expire_seconds  该二维码有效时间，以秒为单位。 最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。
     * action_name 二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久,QR_LIMIT_STR_SCENE为永久的字符串参数值
     * action_info 二维码详细信息
     * scene_id    场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
     * scene_str   场景值ID（字符串形式的ID），字符串类型，长度限制为1到64，仅永久二维码支持此字段   
    */ 
    public static function getManagerCodeImg()
    {
        require Yii::getAlias('@vendor/wx/WxTemplate.php');
        
        $wxTemplate = new \WxTemplate();
        // if (($access_token = session('WxAccessToken')) == null) {
            $access_token = $wxTemplate->getAccessToken();
            session('WxAccessToken', $access_token, 7000);
        // }
		
		// var_dump($access_token);

        // if (($urlCode = session('WxUrlCode_' . u()->id)) == null) {
            $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
            $id = u()->id - 100000;
            $data = ['action_name' => 'QR_LIMIT_SCENE', 'action_info' => ['scene' => ['scene_id' => $id]]];
            $json = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                curl_close($ch);
                return false;
            } else {
                curl_close($ch);
                $object = json_decode($result);
				// var_dump($object);
				// exit();
                $urlCode = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $object->ticket;
                session('WxUrlCode_' . u()->id, $urlCode, 288000);
            }
        // }
        return $urlCode;
    }
}
