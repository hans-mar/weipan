<?php

namespace common\helpers;

use Yii;

class Security
{
    /**
     * 生成哈希密码
     * 
     * @see yii\base\Security::generatePasswordHash()
     */
    public static function generatePasswordHash($password, $cost = null)
    {
        return Yii::$app->getSecurity()->generatePasswordHash($password, $cost);
    }

    /**
     * 验证哈希密码
     * 
     * @see yii\base\Security::validatePassword()
     */
    public static function validatePassword($password, $hash)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $hash);
    }

    /**
     * 可用于页面间传输的加密
     */
    public static function base64encrypt($data)
    {
        return Url::base64encode(static::encrypt($data));
    }

    /**
     * base64encrypt 对应的解密
     */
    public static function base64decrypt($base64encryptedData)
    {
        return static::decrypt(Url::base64decode($base64encryptedData));
    }

    /**
     * 加密
     * 
     * @see yii\base\Security::encryptByPassword()
     */
    public static function encrypt($data, $secretKey = SECRET_KEY)
    {
        return Yii::$app->getSecurity()->encryptByPassword($data, $secretKey);
    }

    /**
     * 解密
     * 
     * @see yii\base\Security::decryptByPassword()
     */
    public static function decrypt($encryptedData, $secretKey = SECRET_KEY)
    {
        return Yii::$app->getSecurity()->decryptByPassword($encryptedData, $secretKey);
    }

    /**
     * 将由安全秘钥和数据生成的哈希串前缀加到数据上
     * 
     * @see yii\base\Security::hashData()
     */
    public static function hashData($genuineData, $secretKey = SECRET_KEY)
    {
        return Yii::$app->getSecurity()->hashData($genuineData, $secretKey);
    }

    /**
     * 验证数据的完整性
     * 
     * @see yii\base\Security::validateData()
     */
    public static function validateData($data, $secretKey = SECRET_KEY)
    {
        return Yii::$app->getSecurity()->validateData($data, $secretKey);
    }
}
