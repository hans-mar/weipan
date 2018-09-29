<?php

namespace api\components;

use Yii;

/**
 * 认证类必须实现包含以下方法的 认证接口 yii\web\IdentityInterface：
 */
class WebUser extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface, \yii\filters\RateLimitInterface
{
    /**
     * 指定用户表
     */
    public static function tableName()
    {
        return '{{%admin_user}}';
    }

    public function getRateLimit($request, $action)
    {
        return [1, 1];
    }

    public function loadAllowance($request, $action)
    {
        return [1, time()];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {

    }

    /**
     * 获取该认证实例表示的用户的ID。
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * 获取基于 cookie 登录时使用的认证密钥。 认证密钥储存在 cookie 里并且将来会与服务端的版本进行比较以确保 cookie的有效性。
     */
    public function getAuthKey()
    {
        return '';
    }

    /**
     * 根据指定的用户ID查找 认证模型类的实例，当你需要使用session来维持登录状态的时候会用到这个方法。
     */
    public static function findIdentity($id)
    {
        return static::find()->select(['id', 'username', 'state', 'realname', 'position_id'])->where('id = :id', [':id' => $id])->one();
    }

    /**
     * 根据指定的存取令牌查找 认证模型类的实例，该方法用于 通过单个加密令牌认证用户的时候（比如无状态的RESTful应用）。
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->select(['id', 'username', 'state', 'realname', 'position_id'])->where('id = :id', [':id' => $token])->one();
    }

    /**
     * 是基于 cookie 登录密钥的 验证的逻辑的实现。
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->setPassword($password) === $this->password;
    }

    public function setPassword($password)
    {
        return md5($this->salt . $password);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }
}
