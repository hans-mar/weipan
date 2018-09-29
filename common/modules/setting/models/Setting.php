<?php

namespace common\modules\setting\models;

use Yii;
use common\models\Option;
use common\helpers\FileHelper;
use common\helpers\ArrayHelper;

class Setting extends \common\components\Model
{
    // 虚拟字段
    public $id;
    public $pid;
    public $name;
    public $var;
    public $value;
    public $type;
    public $alter;
    public $comment;
    public $level;
    public $uploads = [];
    // 配置
    protected static $suffixName = '_settings';
    // 内部变量
    protected static $_settings = null;

    public function rules()
    {
        return [
            [['pid', 'name'], 'required'],
            ['pid', 'integer'],
            ['name', 'string', 'max' => 20],
            [['var', 'type', 'comment'], 'required', 'on' => 'addSetting'],
            ['type', 'in', 'range' => ['text', 'textarea', 'radio', 'checkbox', 'select', 'file', 'custom'], 'on' => 'addSetting'],
            ['alter', 'checkAlter', 'skipOnEmpty' => false, 'on' => 'addSetting'],
            ['var', 'checkVar', 'on' => 'addSetting'],
            ['id', 'setMaxId', 'skipOnEmpty' => false],
            ['level', 'setLevel', 'skipOnEmpty' => false],
        ];
    }

    public function checkVar()
    {
        $settings = self::getWebSettings();

        $name = ArrayHelper::filter($settings, ['eq' => ['var' => $this->var]]);
        if (!empty($name)) {
            $this->addError('var', '这个配置名已经使用了，请换一个');
        }
    }

    public function checkAlter()
    {
        if ($this->type === 'custom') {
            if (strpos($this->alter, ':') !== false) {
                $pieces = explode(':', $this->alter);
                $callback = [$pieces[0], $pieces[1]];
            } else {
                $callback = $this->alter;
            }
            if (!is_callable($callback)) {
                $this->addError('alter', "当配置类型为 [{$this->type}] 时，配置项必须是可回调的方法或函数，格式参考输入框中的提示");
            }
        } elseif (!$this->alter && in_array($this->type, ['select', 'radio', 'checkbox', 'custom'])) {
            $this->addError('alter', "当配置类型为 [{$this->type}] 时，必须设置配置项");
        } elseif ($this->alter) {
            if ($this->type != 'custom') {
                try {
                    $pieces = explode(',', $this->alter);
                    $alter = [];
                    array_walk($pieces, function($item) use (&$alter) {
                        list($key, $value) = explode('=', $item);
                        $alter[$key] = $value;
                    });
                    $this->alter = serialize($alter);
                } catch (\yii\base\ErrorException $e) {
                    $this->addError('alter', "配置项格式不正确，参考输入框中的提示");
                }
            }
        }
    }

    public function setMaxId()
    {
        $settings = self::getWebSettings();
        if (!$settings) {
            $this->id = 1;
        } else {
            ArrayHelper::multisort($settings, 'id', SORT_DESC);
            $this->id = $settings[0]['id'] + 1;
        }
    }

    public function setLevel()
    {
        if ($this->pid == 0) {
            $this->level = 1;
        } else {
            $settings = self::getWebSettings();
            $parent = current(ArrayHelper::filter($settings, ['eq' => ['id' => $this->pid]]));
            $this->level = $parent['level'] + 1;
        }
    }

    public function attributeLabels()
    {
        return [
            'name' => '描述',
            'pid' => '父ID',
            'var' => '配置名',
            'type' => '配置类型',
            'alter' => '配置项',
            'comment' => '配置说明'
        ];
    }

    public static function getConfig()
    {
        return ArrayHelper::map(ArrayHelper::filter(self::getWebSettings(), ['eq' => ['level' => 3]]), 'var', 'value');
    }

    public static function getWebSettings($refresh = false)
    {
        if (self::$_settings === null || $refresh === true) {
            $optionName = FileHelper::getCurrentApp() . self::$suffixName;
            try {
                $settings = self::db("SELECT option_value FROM {{%option}} WHERE option_name='{$optionName}'")->queryOne();
            } catch (\yii\db\Exception $e) {
                self::db("CREATE TABLE {{%option}} (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `option_name` varchar(64) NOT NULL DEFAULT '',
                    `option_value` longtext,
                    `type` tinyint(4) DEFAULT '1',
                    `state` tinyint(4) DEFAULT '1',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `option_name` (`option_name`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;")->execute();
                $settings = self::db("SELECT option_value FROM {{%option}} WHERE option_name='{$optionName}'")->queryOne();
            }
            if ($settings) {
                $settings = unserialize($settings['option_value']);
            } else {
                $settings = [];
                $model = new Option;
                $model->option_name = $optionName;
                $model->option_value = serialize($settings);
                $model->state = Option::STATE_VALID;
                $model->type = Option::TYPE_COMMON;
                $model->insert();
            }
            self::$_settings = $settings;
        }

        return self::$_settings;
    }

    public function add()
    {
        $settings = self::getWebSettings();
        $settings[] = $this->attributes;

        return $this->up($settings);
    }

    public function save()
    {
        $settings = self::getWebSettings();

        foreach ($settings as $key => $setting) {
            if (isset($_POST[$setting['id']])) {
                if (is_array($_POST[$setting['id']])) {
                    $value = implode(',', $_POST[$setting['id']]);
                } else {
                    $value = $_POST[$setting['id']];
                }
                $settings[$key]['value'] = $value;
            } elseif (isset($_FILES['Upload1']['name1'][$setting['id']])) {
                $upload = self::getUpload(['uploadPath' => 'setting', 'extensions' => null, 'uploadName' => $setting['id']]);
                if ($upload->move()) {
                    $settings[$key]['value'] = $upload->filePath;
                    $this->uploads[$setting['id']] = $settings[$key]['value'];
                } else {
                    $this->addError('name', current(ArrayHelper::getColumn($upload->getErrors(), 0)));
                    return false;
                }
            }
        }

        return $this->up($settings);
    }

    public function delete($id)
    {
        $settings = self::getWebSettings();
        ArrayHelper::multisort($settings, 'level');
        $pid = [];
        foreach ($settings as $key => $setting) {
            if (in_array($setting['pid'], $pid) || $setting['id'] == $id) {
                if ($setting['type'] === 'file') {
                    @unlink(Yii::getAlias('@webroot' . $setting['value']));
                }
                unset($settings[$key]);
                $pid[] = $id;
            }
        }

        return $this->up($settings);
    }

    protected function up($settings)
    {
        $model = Option::findOne(['option_name' => FileHelper::getCurrentApp() . self::$suffixName]);
        $model->option_value = serialize($settings);

        return $model->update() !== false;
    }
}
