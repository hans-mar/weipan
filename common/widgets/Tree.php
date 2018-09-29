<?php

namespace common\widgets;

use common\helpers\Html;
use common\helpers\ArrayHelper;
use yii\base\InvalidParamException;

/**
 * 获取树形下拉的小组件，可以获取带层级的下拉列表
 * [with]属性表示相关联的层级表，该表必须有`code`字段
 * 该组件已集成进 common\traits\QueryTrait 中
 * 以下是一个使用例子：
 * 
 * ```php
 * // 关联表用法：
 * $query = \common\models\AdminUser::find()->with('department');
 * $html = $query->getTree(['withName' => ['code' => 'name']])
 *               ->dropDownList('realname');
 * // 或者单表用法：
 * $query = \common\models\Department::find();
 * $html = $query->getTree()
 *               ->dropDownList('name');
 * ```
 * 
 * @author ChisWill
 */
class Tree extends \yii\base\Object
{
    use \common\traits\ChisWill;

    /**
     * @var object 查询对象
     */
    public $query = null;
    /**
     * @var string 关联查询表的别名
     */
    public $with = null;
    /**
     * @var object 关联表的查询条件
     */
    public $withQuery = null;
    /**
     * @var string|array  要显示的关联表字段配置
     * 如果是字符串，则表示以该字段作为每个 <option> 的 html 文本
     * 如果是数组，则形如：['keyField' => 'valueField']，其中
     * 以 `keyField` 字段的值作为每个 <option> 的 [value] 属性
     * 以 `valueField` 字段的值作为每个 <option> 的 html 文本
     */
    public $withName = [];
    /**
     * @var string 主键字段名称，如果 $query 继承自 yii\db\ActiveQuery，将会被自动校正
     */
    public $key = 'id';
    /**
     * @var string 父级主键名称
     */
    public $pid = 'pid';
    /**
     * @var string 排序字段名称
     */
    public $sort = 'sort';
    /**
     * @var object 对应的模型
     */
    public $model = null;
    /**
     * @var array 需要在 items 中出现的列名
     */
    public $select = [];
    /**
     * @var array 选择需要在每个<option>的属性中输出的列
     */
    public $optionAttrs = [];
    /**
     * @var string 关联表主键值前缀，为了区分原表的主键值
     */
    public $withKeyPrefix = 'cw-';
    /**
     * @var array 关联表元素的 HTML 属性
     */
    public $withOptions = [];
    /**
     * @var string|boolean 是否添加提示信息标题
     * -string 指定的文本提示信息
     * -false  不添加提示信息
     * -true   自动设置，如果设置了模型，将会自动填充，否则使用默认文本信息
     */
    public $header = true;
    /**
     * @var array 提示信息标题的 HTML 属性
     */
    public $headerOptions = [];
    /**
     * @var Closure 每添加一个元素前的回调方法
     */
    public $beforeItem = null;

    /************************* 以下属性均为程序运算需要，不要尝试从外部进行复制 *************************/
    /**
     * @var array 下拉标签中的元素数据
     */
    protected $items = [];
    /**
     * @var array 数据库直接查询出来的数据
     */
    protected $data = [];
    /**
     * @var string select标签的name属性名称
     */
    protected $name = '';
    /**
     * @var array 关联表的数据
     */
    protected $withData = [];
    /**
     * @var string 关联表的主键
     */
    protected $withKey;
    /**
     * @var string 关联表的显示字段
     */
    protected $withValue;
    /**
     * @var string name属性的值
     */
    private $_name = null;

    /**
     * 初始化
     */
    public function init()
    {
        parent::init();
        // 初始化模型
        if ($this->query instanceof \yii\db\ActiveQuery && $this->model === null) {
            $this->model = new $this->query->modelClass;
        }
        // 判断是否设置关联表的层级关系输出
        if ($this->with === null && $this->query->with) {
            $this->with = current($this->query->with);
        }
        // 设置关联表相关信息
        if ($this->with) {
            if (!$this->withName) {
                throw new InvalidParamException("通过 \$query 的 `with()` 方法进行关联查询后，必须设置 [withName]，指定要显示的关联表字段！");
            } else {
                if (is_string($this->withName)) {
                    $this->withKey = $this->key;
                    $this->withName = (array) $this->withName;
                } else {
                    $this->withKey = key($this->withName);
                }
                $this->withValue = current($this->withName);
            }
            
            if ($this->withQuery instanceof \yii\db\ActiveQuery) {
                $withModel = new $this->withQuery->modelClass;
            } else {
                $withFunc = 'get' . ucfirst($this->with);
                $withQuery = $this->model->{$withFunc}();
                $withModel = new $withQuery->modelClass;
                $withQuery = $withModel::find();
                if ($this->withQuery instanceof \Closure) {
                    call_user_func($this->withQuery, $withQuery);
                }
                $this->withQuery = $withQuery;
            }
        }
        // 如果有设置模型，获取所有字段
        if ($this->model !== null) {
            if ($this->with) {
                $attributes = $withModel->attributes();
            } else {
                $attributes = $this->model->attributes();
            }
        }
        // 字段名称设置检查
        if (!empty($attributes)) {
            if (!in_array($this->pid, $attributes)) {
                $model = $this->model;
                throw new InvalidParamException("{$model::className()} 对应的表中没有 `{$this->pid}` 字段！");
            }
            if (!in_array($this->sort, $attributes)) {
                $this->sort = null;
            }
            // 自动校正主键
            $this->key = current($this->model->primaryKey());
        }
        // 其他参数初始化
        $this->select = array_unique(array_merge($this->select, $this->optionAttrs));
    }

    /**
     * 以 select 标签形式输出数据
     * 
     * @param  string $name      用作当 select 标签的 [name] 属性的字段名称
     * @param  mixed  $selection 选中值
     * @param  array  $options   配置参数
     * @return string            select 标签的 HTML 代码
     */
    public function dropDownList($name, $selection = '', $options = [])
    {
        $this->name = $name;

        $this->prepareItems();

        foreach ($this->items as $item) {
            $items[$item['_key']] = $item['_text'];
            $item['options'][$item['_key']]['data-text'] = $item['_value'];
            $options['options'][$item['_key']] = $item['options'][$item['_key']];
            foreach ($this->optionAttrs as $attr) {
                $options['options'][$item['_key']]['data-' . $attr] = ArrayHelper::getValue($item, $attr, '');
            }
        }
        $options['encodeSpaces'] = true;

        return Html::dropDownList($this->getName(), $selection, $items, $options);
    }

    /**
     * 获取元数据
     * 
     * @param  string $name 用作当 [name] 属性的字段名称
     * @return array
     */
    public function getItems($name)
    {
        $this->name = $name;

        $this->prepareItems();

        return $this->items;
    }

    /**
     * 添加提示信息标题
     */
    protected function addHeaderItem()
    {
        if ($this->header === true) {
            if ($this->model && method_exists($this->model, 'attributeLabels') && !empty($this->model->attributeLabels()[$this->getName()])) {
                $header = $this->model->attributeLabels()[$this->getName()];
            } else {
                $header = '请选择...';
            }
            array_unshift($this->items, ['_key' => '', '_value' => $header, '_text' => $header, 'options' => ['' => $this->headerOptions]]);
        } elseif (is_string($this->header)) {
            array_unshift($this->items, ['_key' => '', '_value' => $this->header, '_text' => $this->header, 'options' => ['' => $this->headerOptions]]);
        }
    }

    /**
     * 初始化数据
     */
    protected function prepareItems()
    {
        if ($this->with) {
            if ($this->sort) {
                $this->withQuery->addOrderBy($this->sort);
            }
            $this->withData = $this->withQuery->indexBy($this->key)->asArray()->all();
            $this->data = $this->query->asArray()->all();

            $pid = $this->getItemParentId($this->withData);
            $this->setWithItems($pid);
        } else {
            if ($this->sort) {
                $this->query->addOrderBy($this->sort);
            }
            $this->data = $this->query->indexBy($this->key)->asArray()->all();

            $pid = $this->getItemParentId($this->data);
            $this->setItems($pid);
        }

        $this->addHeaderItem();
    }

    /**
     * 设置非关联型的元素
     */
    protected function setItems($pid = '0', $recursionLevel = 0)
    {
        $recursionLevel++;
        foreach ($this->data as $key => $row) {
            if ($row[$this->pid] === $pid) {
                if ($this->beforeItem !== null && !call_user_func($this->beforeItem, $row)) {
                    continue;
                }
                if ($recursionLevel === 1) {
                    $prefix = '★';
                } else {
                    $prefix = str_repeat(' ', ($recursionLevel - 2) * 3) . '┕ ';
                }
                $value = $this->getNameValue($row);
                $item = [
                    '_key' => $row[$this->key],
                    '_text' => $prefix . $value,
                    '_value' => $value,
                ];
                foreach ($this->select as $select) {
                    $item[$select] = $row[$select];
                }
                $this->items[] = $item;
                $this->setItems($row[$this->key], $recursionLevel);
                unset($this->data[$key]);
            }
        }
    }

    /**
     * 设置关联型的元素
     */
    protected function setWithItems($pid = '0', $recursionLevel = 0)
    {
        $recursionLevel++;
        foreach ($this->withData as $key => $row) {
            if ($row[$this->pid] === $pid) {
                $withPrefix = str_repeat(' ', ($recursionLevel - 1) * 3) . '★';
                $withKey = $this->withKeyPrefix . $row[$this->withKey];
                $withValue = $row[$this->withValue];
                $this->items[$withKey] = [
                    '_key' => $withKey,
                    '_text' => $withPrefix . $withValue,
                    '_value' => $withValue,
                    'options' => [$withKey => $this->withOptions]
                ];
                foreach ($this->data as $k => $item) {
                    if ($item[$this->with][$this->key] == $row[$this->key]) {
                        $prefix = str_repeat(' ', ($recursionLevel - 1) * 3) . ' ┕ ';
                        $value = $this->getNameValue($item);
                        $this->items[$item[$this->key]] = [
                            '_key' => $item[$this->key],
                            '_text' => $prefix . $value,
                            '_value' => $value
                        ];
                        foreach ($this->select as $select) {
                            $this->items[$item[$this->key]][$select] = $item[$select];
                        }
                        unset($this->data[$k]);
                    }
                }
                unset($this->withData[$key]);
                $this->setWithItems($row[$this->key], $recursionLevel);
            }
        }
    }

    /**
     * 获取关联表对应字段的值
     */
    protected function getNameValue($item)
    {
        if (strpos($this->name, '.') !== false) {
            $relations = explode('.', $this->name);
            $name = array_pop($relations);
            $row = $item;
            foreach ($relations as $relation) {
                $row = $row[$relation];
            }
            return $row[$name];
        } else {
            return $item[$this->name];
        }
    }

    /**
     * 从别名中获取 name 属性
     */
    protected function getName()
    {
        if ($this->_name === null) {
            if (strpos($this->name, '.') !== false) {
                $relations = explode('.', $this->name);
                $this->_name = array_pop($relations);
            } else {
                $this->_name = $this->name;
            }
        }
        return $this->_name;
    }

    /**
     * 从结果集中获取顶级元素的pid
     */
    protected function getItemParentId($data)
    {
        foreach ($data as $row) {
            if (!array_key_exists($row[$this->pid], $data)) {
                return $row[$this->pid];
            }
        }
    }
}
