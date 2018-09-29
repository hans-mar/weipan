<?php

namespace common\modules\rbac\models;

use Yii;
use common\helpers\Inflector;
use common\helpers\FileHelper;

/**
 * 这是表 `hsh_auth_item` 的模型
 */
class AuthItem extends \common\components\ARModel
{
    public $oldRoleName;
    public $roles = [];
    public $permissions = [];

    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;

    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'default', 'value' => ''],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name', 'type'], 'unique', 'targetAttribute' => ['name', 'type'], 'message' => '该角色已经存在了！'],
            [['name'], 'validateCreateRole', 'on' => 'createRole'],
            [['name'], 'validateUpdateRole', 'on' => 'updateRole'],
            [['rule_name'], 'validateRuleName', 'skipOnEmpty' => false]
        ];
    }

    public function validateCreateRole()
    {
        $auth = Yii::$app->authManager;

        if ($auth->getRole($this->name)) {
            $this->addError('name', '该角色名已经存在！');
        }
    }

    public function validateUpdateRole()
    {
        $auth = Yii::$app->authManager;

        if ($this->name != $this->oldRoleName) {
            if ($auth->getRole($this->name)) {
                $this->addError('name', '该角色名已经存在！');
            }
        }
    }

    public function validateRuleName()
    {
        $auth = Yii::$app->authManager;
        if (empty($this->rule_name)) {
            $this->rule_name = null;
        } elseif (strpos($this->rule_name, '\\') === false) {
            if (!AuthRule::find()->where('name = :name', [':name' => $this->rule_name])->exists()) {
                $this->addError('rule_name', '这个规则不存在！');
            }
        } elseif (class_exists($this->rule_name)) {
            $rule = new $this->rule_name;
            if ($rule instanceof \yii\rbac\Rule) {
                if (!$auth->getRule($rule->name)) {
                    $auth->add($rule);
                }
                $this->rule_name = $rule->name;
            } else {
                $this->addError('rule_name', '规则类必须继承自 yii\rbac\Rule');
            }
        } else {
            $this->addError('rule_name', '该类不存在！');
        }
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'type' => 'Type',
            'description' => '描述',
            'rule_name' => '规则',
            'data' => 'Data',
            'created_at' => '创建时间',
            'updated_at' => '最后修改时间',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name'])->joinWith('childItem')->orderBy('type ASC');
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'authItem.type' => $this->type,
                'authItem.created_at' => $this->created_at,
                'authItem.updated_at' => $this->updated_at,
                ])
            ->andFilterWhere(['like', 'authItem.name', $this->name])
            ->andFilterWhere(['like', 'authItem.description', $this->description])
            ->andFilterWhere(['like', 'authItem.rule_name', $this->rule_name])
            ->andFilterWhere(['like', 'authItem.data', $this->data])
        ;
    }

    public static function getRoleQuery()
    {
        return self::find()
            ->with('children')
            ->where(['type' => self::TYPE_ROLE, 'description' => FileHelper::getCurrentApp()])
            ->indexBy('name')
            ->orderBy('name');
    }
    
    public static function getPermissionQuery()
    {
       return self::find()->where(['type' => self::TYPE_PERMISSION]);
    }

    /****************************** 以下为公共操作的方法 ******************************/

    public static function getFileActionList($app = null, $filterCtrlList = [])
    {
        $appName = FileHelper::getCurrentApp();
        $app = $app ?: $appName;
        $basePath = Yii::getAlias('@' . $app);
        $controllerPath = $basePath . DIRECTORY_SEPARATOR . 'controllers';
        $files = FileHelper::findFiles($controllerPath, ['only' => ['suffix' => '*.php']]);

        $result = [];
        foreach ($files as $filePath) {
            $controllerName = basename($filePath, 'Controller.php');
            if (in_array(lcfirst($controllerName), $filterCtrlList)) {
                continue;
            }
            $content = file_get_contents($filePath);
            $matchNums = preg_match_all('~(?:/\*\*[\r\n].*@authname\s*?(.*)\n.*)?public\s*function\s*action([A-Z]\w*)\s*\(~Us', $content, $matches, PREG_SET_ORDER);
            if ($matchNums > 0) {
                foreach ($matches as $key => $match) {
                    $result[$appName . $controllerName . $match[2]] = $match[1];
                }
            }
        }

        return $result;
    }

    public static function getAuthItemQuery()
    {
        return self::getPermissionQuery()
            ->select(['name', 'type', 'description', 'rule_name', 'data'])
            ->andWhere('name REGEXP BINARY "' . FileHelper::getCurrentApp() . '[A-Z]\w*"')
            ->orderBy('name ASC');
    }

    public static function getCurrentAuthItems()
    {
        return self::getAuthItemQuery()->all();
    }

    public static function getGroupPermissionData()
    {
        $data = self::getCurrentAuthItems();
        $result = [];
        foreach ($data as $index => $permission) {
            $name = Inflector::camel2id($permission->name);
            $pieces = explode('-', $name);
            $app = array_shift($pieces);
            $controller = array_shift($pieces);
            $action = Inflector::id2camel(implode('-', $pieces));
            if (u()->can($controller . '/' . $action)) {
                $result[$controller][$permission->name] = $permission->description;
            }
        }

        return $result;
    }

    public static function filterLoopRoles(&$roles, $parent)
    {
        $auth = Yii::$app->authManager;
        $allRoles = $auth->getRoles();
        $allRoles = array_filter($allRoles, function ($role) use ($roles) {
            return in_array($role->name, $roles);
        });
        foreach ($allRoles as $key => $role) {
            if (self::detectLoop($parent, $role)) {
                unset($roles[$role->name]);
            }
        }
    }

    protected static function detectLoop($parent, $child)
    {
        $auth = Yii::$app->authManager;

        if ($child->name === $parent->name) {
            return true;
        }
        foreach ($auth->getChildren($child->name) as $grandchild) {
            if (self::detectLoop($parent, $grandchild)) {
                return true;
            }
        }
        return false;
    }

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/

    // Map method of field `type`
    public static function getTypeMap($prepend = false)
    {
        $map = [
            self::TYPE_ROLE => '角色',
            self::TYPE_PERMISSION => '权限'
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `type`
    public function getTypeValue($value = null)
    {
        return $this->resetValue($value);
    }
}
