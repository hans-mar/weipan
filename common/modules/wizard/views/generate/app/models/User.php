<?php
echo "<?php\n";
?>

namespace <?= $namespace ?>;

use Yii;
use <?= $appName ?>\components\WebUser;

class User extends \common\models\User
{
    public $oldPassword;
    public $newPassword;
    public $cfmPassword;
    public $rememberMe;
    public $captcha;

    protected $_identity;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['password', 'newPassword'], 'match', 'pattern' => '/[a-z0-9~!@#$%^]{6,}/Ui', 'on' => ['register', 'password'], 'message' => '{attribute}至少6位'],
            [['oldPassword', 'newPassword', 'cfmPassword'], 'required'],
            [['oldPassword'], 'validateOldPassword'],
            [['newPassword'], 'compare', 'compareAttribute' => 'cfmPassword'],
            [['captcha'], 'captcha'],
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'register' => ['username', 'password', 'captcha'],
            'login' => ['username', 'password'],
            'password' => ['oldPassword', 'newPassword', 'cfmPassword'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'oldPassword' => '旧密码',
            'newPassword' => '新密码',
            'cfmPassword' => '确认密码',
            'captcha' => '验证码',
        ]);
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

    protected function getIdentity()
    {
        if ($this->_identity === null) {
            $this->_identity = WebUser::findByUsername($this->username);
        }

        return $this->_identity;
    }

    public function login($runValidation = true)
    {
        if ($runValidation && !$this->beforeLogin()) {
            return !$this->hasErrors();
        }

        return user()->login($this->getIdentity(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    }

    public function validateOldPassword()
    {
        if (!u()->validatePassword($this->oldPassword)) {
            $this->addError('oldPassword', '旧密码不正确');
        }
    }
}
