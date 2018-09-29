<?php

namespace common\models;

use Yii;
use yii\log\Logger;

/**
 * 这是表 `log` 的模型
 */
class Log extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['level'], 'integer'],
            [['log_time'], 'number'],
            [['prefix', 'message'], 'default', 'value' => ''],
            [['category'], 'string', 'max' => 255]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => 'Level',
            'category' => 'Category',
            'log_time' => 'Log Time',
            'prefix' => 'Prefix',
            'message' => 'Message',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    // public function getRelation()
    // {
    //     return $this->hasOne(Class::className(), ['foreign_key' => 'primary_key']);
    // }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'log.id' => $this->id,
                'log.level' => $this->level,
                'log.log_time' => $this->log_time,
            ])
            ->andFilterWhere(['like', 'log.category', $this->category])
            ->andFilterWhere(['like', 'log.prefix', $this->prefix])
            ->andFilterWhere(['like', 'log.message', $this->message])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    /**
     * 格式化消息日志前缀
     */
    public static function formatPrefix($message)
    {
        if (Yii::$app === null || Yii::$app instanceof yii\console\Application) {
            return '';
        }

        $request = Yii::$app->getRequest();
        $method = $request->getMethod() ?: '-';
        $url = $request->getHostInfo() . $request->getUrl();
        $ip = $request->getUserIP() ?: '-';

        $user = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
        if ($user && ($identity = $user->getIdentity(false))) {
            $userID = $identity->getId();
        } else {
            $userID = '-';
        }

        $session = Yii::$app->has('session', true) ? Yii::$app->get('session') : null;
        $sessionID = $session && $session->getIsActive() ? $session->getId() : '-';

        return "[$method][$url][$ip][$userID][$sessionID]";
    }

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/

    // Map method of field `level`
    public static function getLevelMap($prepend = false)
    {
        $map = [
            Logger::LEVEL_ERROR => 'Error',
            Logger::LEVEL_WARNING => 'Warning',
            Logger::LEVEL_INFO => 'Info',
            Logger::LEVEL_TRACE => 'Trace',
            Logger::LEVEL_PROFILE => 'Profile',
            Logger::LEVEL_PROFILE_BEGIN => 'Profile begin',
            Logger::LEVEL_PROFILE_END => 'Profile end'
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `level`
    public function getLevelValue($value = null)
    {
        return $this->resetValue($value);
    }
}
