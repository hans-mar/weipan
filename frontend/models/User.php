<?php

namespace frontend\models;

use Yii;
use frontend\components\WebUser;

class User extends \common\models\User
{
    // 虚拟字段
    public $oldPassword;
    public $newPassword;
    public $cfmPassword;
    public $rememberMe;
    public $verifyCode;
    public $captcha;

    protected $_identity;

    public function rules()
    {
        return array_merge(parent::rules(), [
            // 密码规则，注册和修改密码时复用同一个规则
            [['password', 'newPassword'], 'match', 'pattern' => '/[a-z0-9~!@#$%^]{6,}/Ui', 'on' => ['register', 'password', 'forget'], 'message' => '{attribute}至少6位'],
            // 注册场景的基础验证
            [['cfmPassword', 'verifyCode'], 'required', 'on' => ['register', 'forget']],
            //第一次填写手机号
            [['mobile', 'verifyCode'], 'required', 'on' => ['setMobile']],
            // 注册场景密码和确认密码的验证
            [['password'], 'compare', 'compareAttribute' => 'cfmPassword', 'on' => ['register', 'forget', 'setPassword']],
            
            // 修改密码场景的基础验证
            [['oldPassword', 'newPassword', 'cfmPassword'], 'required', 'on' => 'password'],
            // 修改密码验证旧密码
            [['oldPassword'], 'validateOldPassword'],
            // 修改密码场景新密码与验证密码的验证
            [['newPassword'], 'compare', 'compareAttribute' => 'cfmPassword'],
            // 短信验证码
            [['verifyCode'], 'verifyCode'],
            // 验证码
            [['captcha'], 'captcha'],
            // 其他规则
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'register' => ['username', 'password', 'cfmPassword', 'mobile', 'verifyCode'],
            'login' => ['username', 'password', 'rememberMe'],
            'password' => ['oldPassword', 'newPassword', 'cfmPassword'],
            'forget' => ['password', 'cfmPassword', 'verifyCode'],
            'changePhone' => ['mobile', 'verifyCode'],
            'setPassword' => ['password', 'cfmPassword'],
            'setMobile' => ['mobile', 'verifyCode'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'oldPassword' => '旧密码',
            'newPassword' => '新密码',
            'cfmPassword' => '确认密码',
            'rememberMe' => '记住我',
            'verifyCode' => '短信验证码',
            'captcha' => '验证码',
        ]);
    }

    public function verifyCode()
    {
        if ($this->verifyCode != session('verifyCode')) {
            $this->addError('verifyCode', '短信验证码不正确');
        }
    }

    public function validateOldPassword()
    {
        if (!u()->validatePassword($this->oldPassword)) {
            $this->addError('oldPassword', '旧密码不正确');
        }
    }

    protected function beforeLogin()
    {
        if (!$this->username) {
            $this->addError('username', '请输入用户名');
        }
        if (!$this->password) {
            $this->addError('password', '请输入密码');
        }
        if ($this->hasErrors()) {
            return false;
        }
        $identity = $this->getIdentity();
        if (!$identity || !$identity->validatePassword($this->password)) {
            $this->addError('password', '用户名或密码错误');
            return false;
        } else {
            return true;
        }
    }
    protected function loginCheck()
    {
        if (!$this->username) {
            $this->addError('username', '请输入用户名');
        }
        if (!$this->password) {
            $this->addError('password', '请输入密码');
        }
        if ($this->hasErrors()) {
            return false;
        }
        $user = $this->getUser();
        
        
        if (!$user || $user->password!=md5($this->password)) {
            $this->addError('error', '错误的用户名或密码.');
            return false;
        } else {
            return true;}
    }
    

    protected function getIdentity()
    {
        if ($this->_identity === null) {
            $this->_identity = WebUser::find()->where(['open_id' => $this->open_id])->one();
        }

        return $this->_identity;
    }

    protected function getUser()
    {
        if ($this->_identity === null) {
            $this->_identity = WebUser::find()->where(['username' => $this->username])->one();
        }

        return $this->_identity;
    }

    public function login($runValidation = true)
    {
        if ($runValidation && !$this->beforeLogin()) {
            return !$this->hasErrors();
        }
        session('verifyCode', null);

        return user()->login($this->getIdentity(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    }


    public function handlogin()
    {
       //return $this->password;
       if (!$this->loginCheck()) {
        return !$this->hasErrors();
    }

        return user()->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    }

    public static function getWeChatUser($code = '')
    {
        $files = \common\helpers\FileHelper::findFiles(Yii::getAlias('@vendor/wx'), ['only' => ['suffix' => '*.php']]);
        array_walk($files, function ($file) {
            require_once $file;
        });

        $info = session('wechat_userinfo');
        if (empty($info)) {
            if (!empty($code)) {
                $wx = new \WxTemplate();
                $info = $wx->getWechatUser($code);

                session('wechat_userinfo', $info, 144000);
            } else {
                test('请在微信里登录！');
                return false;
            }
        }
    }

    //微信注册用户
    public static function registerUser($code = '', $uid = 0)
    {
        //session微信数据
        self::getWeChatUser($code);
        $wx = session('wechat_userinfo');
        $user = User::find()->where(['open_id' => $wx['openid']])->one();
        $pUser = User::findOne($uid);
        if (empty($user)) {
            $user = new User();
            $user->face = $wx['headimgurl'];
            $user->nickname = $wx['nickname'];
            $user->open_id = $wx['openid'];
            $user->username = 0;
            $user->mobile = 0;
            $user->password = 0;

            if(!empty($pUser)) {
                $user->pid = $pUser->id;
                $user->admin_id = $pUser->admin_id;
            }

            $user->insert(false);
            $user = User::find()->where(['open_id' => $wx['openid']])->one();
        }
        //如果是消息推送进来的，没有头像 
        if ($user->face == 0) {

            if (empty($user->pid) && !empty($pUser)) {
                $user->pid = $pUser->id;
                $user->admin_id = $pUser->admin_id;
            }

            $user->face = $wx['headimgurl'];
            $user->nickname = $wx['nickname'];
            $user->update();
        }
        $user->login(false);
    }

    public static function addUser($uid = 0)
    {
        $wx = session('wechat_userinfo');
        
        $user = new User();
        $user->face = $wx['headimgurl'];
        $user->nickname = $wx['nickname'];
        $user->open_id = $wx['openid'];
        $user->username = 0;
        $user->mobile = 0;
        $user->password = 0;

        $pUser = User::findOne($uid);
        if(!empty($pUser)) {
            $user->pid = $pUser->id;
            $user->admin_id = $pUser->admin_id;
        }

        $user->insert(false);
        $user = User::find()->where(['open_id' => $wx['openid']])->one();
        
        $user->login(false);
    }

    public static function addReg($data)//
    {
        // $user = new User();
        // $user->username=$data['mobile'];
        // $user->nickname = $data['username'];
        // $user->mobile = $data['mobile'];
        // $user->password = md5($data['password']);
        // $user->sim_account=$data['sim_account'];

        $user = new User();
        $user->username=$data['mobile'];
        $user->nickname = $data['username'];
        $user->mobile = $data['mobile'];
        $user->password = md5($data['password']);
        $user->pid=$data['pid'];
        $user->admin_id=$data['admin_id'];
        $user->sim_account=$data['sim_account'];
        $user->insert(false);
        return $user;
        $user->insert(false);
        return $user;
    }
     public static function passforget($data)//passforget
    {
        $user = User::find()->where(['username'=>$data['mobile']])->one();
        //$user->username=$data['mobile'];
        $user->password = md5($data['password']);
        $user->save(false);
        return $user;

    }

    //是否增加一个新用户
    public static function isAddUser($openid, $pid = 0)
    {
        $user = User::find()->where(['open_id' => $openid])->one();

        if (empty($user)) {
            $user = new User();
            $user->face = 0;
            $user->nickname = 0;
            $user->open_id = $openid;
            $user->username = 0;
            $user->mobile = 0;
            $user->password = 0;
            $user->insert(false);
        }
        l('增加一个新用户');
        $isUser = User::findOne($pid);
        if (!empty($isUser)) {
            if (empty($user->pid)) {
                $user->pid = $isUser->id;
            }
            //如果不是经纪人
            if (empty($user->admin_id)) {
                $user->admin_id = $isUser->admin_id;
            }
            $user->update();
        }
    }

    //直属客户搜索
    public function customerQuery($array)
    {
        $this->load(get());
        return $this->search()
                    ->andWhere(['in', 'id', $array])
                    ->andWhere(['state' => self::STATE_VALID])
                    ->andFilterWhere(['like', 'mobile', $this->mobile])
                    ->orderBy('created_at DESC');
    }
}
