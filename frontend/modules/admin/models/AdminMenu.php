<?php

namespace admin\models;

use Yii;

class AdminMenu extends \common\models\Menu
{
    private static $_categoryMap = null;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['icon', 'string', 'max' => 250],
            ['is_show', 'default', 'value' => self::IS_SHOW_YES],
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
            'icon' => '图标',
            // 'field2' => 'description2',
        ]);
    }

    public static function categoryMap()
    {
        if (self::$_categoryMap === null) {
            self::$_categoryMap = self::find()
                ->andWhere(['pid' => 0])
                ->map('url', 'name');
        }

        return self::$_categoryMap;
    }

    public static function showMenu()
    {
        return self::find()
            ->andWhere(['is_show' => self::IS_SHOW_YES])
            ->andWhere(['state' => self::STATE_VALID])
            ->orderBy('level, sort')
            ->all();
    }

    public function beforeAddMenuItem()
    {
        $parent = self::findOne($this->pid);
        if ($parent) {
            $this->level = $parent->level + 1;
        }
    }
}
