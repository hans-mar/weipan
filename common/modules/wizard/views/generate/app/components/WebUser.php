<?php
echo "<?php\n";
?>

namespace <?= $namespace ?>;

use Yii;

/**
 * <?= $appName ?> 用户认证类
 */
class WebUser extends \common\components\Identity
{
    public static function tableName()
    {
        return '{{%user}}';
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }
}
