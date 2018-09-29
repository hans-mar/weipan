<?php

namespace common\components;

use Yii;
use common\helpers\Hui;
use common\helpers\Inflector;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use yii\validators\Validator;

/**
 * 前后台AR模型的基类
 *
 * @author ChisWill
 */
class ARModel extends \yii\db\ActiveRecord
{
    use \common\traits\ChisWill;
    use \common\traits\ModelTrait;

    // 公共模型常量
    const STATE_VALID = 1;
    const STATE_INVALID = -1;
    // 不进行验证的属性、规则列表
    protected $_exceptRules = [];

    public function init()
    {
        parent::init();
        // 非生产环境下的操作
        if (!YII_ENV_PROD) {
            $publicVars = Yii::getObjectVars($this);
            foreach ($this->attributes() as $field) {
                if (method_exists($this, $method = 'get' . ucfirst($field))) { // 检测模型中的方法和字段对应的getter方法是否重名
                    throw new \yii\base\Exception("{$this::className()}::{$method}() 与字段 $field 的 getter 方法命名重复，请更改方法名或字段名！");
                } elseif (array_key_exists($field, $publicVars)) { // 检测属性名和字段名是否重复
                    throw new \yii\base\Exception("{$this::className()}中的公共属性 \${$field} 和字段名重复，请修改属性名或字段名！");
                }
            }
        }
    }
    
    public function behaviors()
    {
        return [
            // AR模型的 插入/更新 前的行为，将会自动填充 created, updated 等字段
            \common\behaviors\ARSaveBehavior::className()
        ];
    }

    /****************************** 以下是公共字段的映射定义和格式化输出范例 ******************************/

    public static function getStateMap($prepend = false)
    {
        $map = [
            self::STATE_VALID => '有效',
            self::STATE_INVALID => '无效'
        ];

        return self::resetMap($map, $prepend);
    }

    public function getStateValue($value = null)
    {
        return $this->resetValue($value);
    }

    /****************************** 以下是公共基础方法 ******************************/

    /**
     * 重置字段的映射，添加默认值
     * 
     * @param  array          $map     映射数组
     * @param  boolean|string $prepend 要添加的默认值
     * @return array                   添加完默认值的字段映射数组
     */
    protected static function resetMap($map, $prepend = false)
    {
        if ($prepend !== false) {
            $prepend === true && $prepend = '全部';
            $map = ['' => $prepend] + $map;
        }

        return $map;
    }

    /**
     * 重置字段的值，默认从字段的映射数组获取
     * 
     * @param  mixed  $value      要重置的字段的值，如果为null，则表示使用模型中对应字段的值
     * @param  string $emptyValue 如果不能从字段映射中获取到值，则返回的默认值
     * @return mixed              重置后的值
     */
    protected function resetValue($value = null, $emptyValue = '')
    {
        // 获取调用的方法
        $valueMethod = debug_backtrace()[1]['function'];
        // 找到对应的字段
        preg_match('/^get(.*)Value$/U', $valueMethod, $mapMethod);
        $field = $mapMethod[1];
        // 拼接对应的map方法
        $mapMethod = 'get' . $field . 'Map';
        // 得到map
        $map = static::$mapMethod();
        // 只有value为null的时候，才使用模型中对应字段的值
        $value === null && $value = $this->{Inflector::camel2id($field, '_')};
        // 从map中获取该value的输出值，如果不存在则返回空字符串
        return ArrayHelper::getValue($map, $value, $emptyValue);
    }

    /**
     * 设置不进行验证的属性以及对应的规则，以下为使用示例：
     * ```php
     * $model->exceptRules = ['required' => ['mobile', 'username'], 'string' => 'password'];
     * ```
     * @param  array $exceptRules 需要排除验证的规则与属性
     * @return $this
     */
    public function setExceptRules(array $exceptRules = [])
    {
        $this->_exceptRules = $exceptRules;

        return $this;
    }

    /**
     * 覆写父类方法，增加了对排除规则的判断
     *
     * @see yii\base\Model::createValidators()
     */
    public function createValidators()
    {
        $validators = new \ArrayObject;
        foreach ($this->rules() as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                $attributes = (array) $rule[0];
                if (isset($this->_exceptRules[$rule[1]])) {
                    $attributes = array_diff($attributes, (array) $this->_exceptRules[$rule[1]]);
                    if (!$attributes) {
                        continue;
                    }
                }
                $validator = Validator::createValidator($rule[1], $this, $attributes, array_slice($rule, 2));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }
        return $validators;
    }

    /**
     * 在更新记录的情况下，默认只验证修改过的字段
     * 
     * @see yii\base\Model::validate()
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        if (!$this->isNewRecord && $attributeNames === null && $this->dirtyAttributes) {
            $attributeNames = array_keys($this->dirtyAttributes);
        }
        return parent::validate($attributeNames, $clearErrors);
    }

    /**
     * 覆写父类方法，目的在于改为实例化自定义的 common\componets\ARQuery，并设置表的别名
     */
    public static function find()
    {
        $modelClass = get_called_class();

        return Yii::createObject(ARQuery::className(), [$modelClass])->from([static::getModelAlias($modelClass) => $modelClass::tableName()]);
    }

    /**
     * 覆写父类方法，目的在于设定关联表的别名，设定为方法名，且优先调用当前项目下对应的模型
     */
    public function hasOne($class, $link)
    {
        // 获取调用的方法
        $method = debug_backtrace()[1]['function'];
        // 获取关联名
        preg_match('/^get(.*)$/U', $method, $res);
        // 获取别名
        $alias = lcfirst($res[1]);
        // 获取关联类名
        $className = StringHelper::basename($class);
        // 获取调用的类名
        $callClass = get_called_class();
        // 获取调用类的命名空间
        $namespace = strstr($callClass, StringHelper::basename($callClass), true);
        // 优先选取当前命名空间下存在的类进行关联
        if (class_exists($namespace . $className)) {
            $class = $namespace . $className;
        }

        return parent::hasOne($class, $link)->from([$alias => $class::tableName()]);
    }

    /**
     * 覆写父类方法，目的在于设定关联表的别名，设定为方法名，且优先调用当前项目下对应的模型
     */
    public function hasMany($class, $link)
    {
        // 获取调用的方法
        $method = debug_backtrace()[1]['function'];
        // 获取关联名
        preg_match('/^get(.*)$/U', $method, $res);
        // 获取别名
        $alias = lcfirst($res[1]);
        // 获取关联类名
        $className = StringHelper::basename($class);
        // 获取调用的类名
        $callClass = get_called_class();
        // 获取调用类的命名空间
        $namespace = strstr($callClass, StringHelper::basename($callClass), true);
        // 优先选取当前命名空间下存在的类进行关联
        if (class_exists($namespace . $className)) {
            $class = $namespace . $className;
        }

        return parent::hasMany($class, $link)->from([$alias => $class::tableName()]);
    }

    /**
     * 内嵌了错误处理的模型获取方法
     * 
     * @param  mixed $condition
     * @return object
     */
    public static function findModel($condition = null)
    {
        if (!$condition) {
            return new static;
        }

        $model = static::findOne($condition);

        if ($model === null) {
            throwex('Not Found');
        }
        
        return $model;
    }

    /**
     * 设置上传的文件信息
     * 
     * @param string $field 上传文件的[name]属性
     * @return object
     */
    public function setUploadedFile($field)
    {
        $modelName = StringHelper::basename(get_called_class());
        $file = ArrayHelper::getValue($_FILES, $modelName . '.name', []);
        $isMultiple = $file && isset($file[$field]) && is_array($file[$field]);
        if ($isMultiple) {
            $this->$field = \common\widgets\UploadedFile::getInstances($this, $field);
        } else {
            $this->$field = \common\widgets\UploadedFile::getInstance($this, $field);
        }

        return $this;
    }

    /**
     * 根据请求中的参数为模型中的字段批量赋值
     * 
     * @param  string $name 搜索参数的name前缀值
     * @return object       当前对象
     */
    protected function setSearchParams($name = 'search')
    {
        foreach (get($name, []) as $field => $value) {
            try {
                $this->$field = $value;
            } catch (\yii\base\UnknownPropertyException $e) {
                // do nothing...
            }
        }

        return $this;
    }

    /**
     * @see common\componets\ARQuery::map()
     */
    public static function map($key, $value = null)
    {
        $className = get_called_class();

        return $className::find()->map($key, $value);
    }

    /**
     * 切换指定字段的逻辑值
     * 
     * @param  string  $field 字段名称
     * @return boolean
     */
    public function toggle($field)
    {
        if ($this->$field == static::STATE_VALID) {
            $this->$field = static::STATE_INVALID;
        } else {
            $this->$field = static::STATE_VALID;
        }
        return $this->update();
    }

    /**
     * 快捷生成表单标题
     *
     * @param string $label   表单标题
     * @param array  $options 标题属性
     * @return string
     */
    public function title($label = '', $options = [])
    {
        $label = ($this->isNewRecord ? '添加' : '编辑') . $label;
        $tag = ArrayHelper::remove($options, 'tag', 'h2');
        $options['style'] = (array) ArrayHelper::getValue($options, 'style');
        $options['style']['text-align'] = 'center';

        return Hui::$tag($label, $options);
    }

    /**
     * Linkage 组件的默认逻辑删除方法
     * 当对应表存在逻辑有效值 state 时，将会自动调用该方法进行删除操作
     * 
     * @param  array       $ids 要删除的元素的主键序列
     * @return true|string      成功则返回true，失败则返回错误原因
     */
    public function deleteLinkage($ids)
    {
        $primaryKey = current($this->primaryKey());
        if (self::dbUpdate($this::tableName(), ['state' => self::STATE_INVALID], [$primaryKey => $ids])) {
            return true;
        } else {
            return '已经删除了！';
        }
    }

    /**
     * 获取模型的别名
     * 
     * @param  string $modelClass 要获取别名的模型类名
     * @return string
     */
    protected static function getModelAlias($modelClass)
    {
        return lcfirst(StringHelper::basename($modelClass));
    }
}
