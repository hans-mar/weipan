<?php

namespace common\components;

use Yii;
use common\helpers\FileHelper;

/**
 * 登录用户认证基础类
 *
 * @author ChisWill
 */
class Identity extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * 指定用户数据属于哪个表
     * 
     * @return string
     */
    public static function tableName()
    {
        throw new \yii\base\UserException('必须重写本方法指定表名！');
    }

    /**
     * 判断是否是程序员登入
     * 
     * @return boolean
     */
    public function isMe()
    {
        return $this->id === 1 && $this->username === 'ChisWill' && get_called_class() === 'admin\components\AdminWebUser';
    }

    /**
     * 判断是否是领导层
     * 
     * @param  callable $extraLimitCallback 额外的判断条件，必须是可回调的方法或函数
     * @return boolean
     */
    public function isMaster($extraLimitCallback = null)
    {
        return $this->id === 1 || ($extraLimitCallback === null ? false : call_user_func($extraLimitCallback));
    }

    /**
     * 快捷调用 yii\web\User:can() ，并简化了调用时的参数
     *
     * @see yii\web\User:can()
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        if (strpos($permissionName, '/') !== false) {
            list($controller, $action) = explode('/', $permissionName);
            $permissionName = $controller . ucfirst($action);
        }

        $permissionName = FileHelper::getCurrentApp() . ucfirst($permissionName);

        return Yii::$app->user->can($permissionName, $params, $allowCaching);
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
        return static::findOne($id);
    }

    /**
     * 根据指定的存取令牌查找 认证模型类的实例，该方法用于 通过单个加密令牌认证用户的时候（比如无状态的RESTful应用）。
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new \yii\base\NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * 是基于 cookie 登录密钥的 验证的逻辑的实现。
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 密码验证的标准实现方式
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}
