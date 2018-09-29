<?php

namespace common\widgets;

use Yii;
use common\helpers\Html;
use common\helpers\Security;
use common\helpers\Inflector;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use yii\base\InvalidParamException;

/**
 * 根据 yii\db\Query 生成无限级列表，并附带有排序、快速添加、编辑等功能.
 * 如下是一个基本的应用方式：
 *
 * ```php
 * $query = Department::find();
 * $html = $query->getLinkage([
 *     'id',
 *     'name' => ['value' => function ($value, $key) {}],
 *     'state' => ['type' => 'select', 'items' => ['User', 'getStateMap']],
 *     // ...
 * ])
 * ```
 *
 * 注意事项：
 * 1.仅支持单主键
 * 2.如果结果集中包含存在 `sort` 或 `code` 字段，且未设置值，则它们将被初始化
 * 
 * @author ChisWill
 */
class Linkage extends \yii\base\Widget
{
    use \common\traits\ChisWill;

    /**
     * @var string 主键字段名称
     */
    public $key = 'id';
    /**
     * @var string 父ID字段名称
     */
    public $pid = 'pid';
    /**
     * @var string 排序字段名称
     */
    public $sort = 'sort';
    /**
     * @var string 从属路径字段名称
     */
    public $code = 'code';
    /**
     * @var yii\db\Query 查询对象
     */
    public $query = null;
    /**
     * @var array 列表每个字段的配置
     */
    public $columns = [];
    /**
     * @var yii\db\ActiveRecord 模型
     */
    public $model = null;
    /**
     * @var boolean 是否允许开启拖拽排序功能
     */
    public $dragSort = true;
    /**
     * @var string 存放数据逻辑有效的字段
     */
    public $activeField = 'state';
    /**
     * @var integer 数据有效的逻辑值
     */
    public $activeValue = 1;
    /**
     * @var boolean 是否使用排序字段
     */
    public $isSort = true;
    /**
     * @var integer 限制允许添加的最大层级数，`0`表示不限制
     */
    public $maxLevel = 0;
    /**
     * @var string 无数据时的显示文本信息
     */
    public $emptyText = '暂无数据（可以先在数据库中录入第一条数据……）';
    /**
     * @var array the HTML attributes for the emptyText of the list view.
     * The "tag" element specifies the tag name of the emptyText element and defaults to "div".
     */
    public $emptyTextOptions = ['class' => 'linkage-empty'];
    /**
     * @var array the HTML attributes for the container tag of the list view.
     * The "tag" element specifies the tag name of the container element and defaults to "div".
     */
    public $containerOptions = ['class' => 'linkage-container'];
    /**
     * @var array 每个ul标签的属性配置
     */
    public $ulOptions = ['class' => 'linkage-ul', 'encode' => false];
    /**
     * @var array the HTML attributes for the summary of the list view.
     * The "tag" element specifies the tag name of the summary element and defaults to "div".
     */
    public $summaryOptions = ['class' => 'linkage-summary'];
    /**
     * @var array 头部行属性
     */
    public $headerOptions = ['class' => 'linkage-header'];
    /**
     * @var array 头部列属性
     */
    public $headerColOptions = [];
    /**
     * @var array|Closure the HTML attributes for the table body rows. This can be either an array
     * specifying the common HTML attributes for all body rows, or an anonymous function that
     * returns an array of the HTML attributes. The anonymous function will be called once.
     */
    public $rowOptions = ['class' => 'linkage-row'];
    /**
     * @var string 操作栏标题栏
     */
    public $actionHeader = '操作';
    /**
     * @var array 禁止的操作栏按钮序列，可选值以下值的组合
     * -add
     * -addChild
     * -delete
     */
    public $forbiddenActions = [];
    /**
     * @var string 替换默认删除元素的方法，必须为当前模型已经存在的方法
     */
    public $deleteMethod = 'deleteLinkage';
    /**
     * @var string 添加元素前的回调方法，必须为当前模型已经存在的方法
     */
    public $beforeAdd = null;
    /**
     * @var string 页面布局模版，可以通过修改模版，设置自定义的标签块，来定制个性化的布局
     * 以下都是内置标签块：
     * - `{summary}`: the summary section. See [[renderSummary()]].
     * - `{items}`: the list items. See [[renderItems()]].
     */
    public $layout = "{summary}\n{items}";
    /**
     * @var array 自定义标签块的回调方法，键值对的形式，eg: ['{calendar}' => function () {return 'something';}]
     */
    public $layoutCallback = [];

    /************************* 以下属性均为程序运算需要，不要尝试从外部进行复制 *************************/
    /**
     * @var integer 数据总数
     */
    protected $count = 0;
    /**
     * @var array 数据源
     */
    protected $data = [];
    /**
     * @var array 以 pid 为键的多维数组
     */
    protected $pidMapData = [];
    /**
     * @var string 最顶级父元素主键值序列
     */
    protected $topParentIds = [];
    /**
     * @var string `code` 字段的分隔符
     */
    protected static $delimiter = '-';

    /**
     * 初始化总入口
     */
    public function init()
    {
        parent::init();

        $this->initModel();

        $this->initData();

        $this->initColumns();
    }

    /**
     * 初始化模型
     */
    protected function initModel()
    {
        if (is_string($this->model)) {
            if (class_exists($this->model)) {
                $this->model = new $this->model;
            } else {
                throw new InvalidParamException("{$this->model} 并不是有效的类名！");
            }
        } elseif (!is_object($this->model) && $this->query->modelClass !== null) {
            $this->model = new $this->query->modelClass;
        }
        // 当存在模型时，会自动修正主键字段，默认选择第一个主键
        if ($this->model) {
            $this->key = current($this->model->primaryKey());
            // 当存在删除替换方法时，检查是否具有表示逻辑有效的字段，如果没有则移除 [deleteMethod] 的默认值
            if ($this->deleteMethod) {
                if (!in_array($this->activeField, $this->model->attributes())) {
                    if ($this->deleteMethod === 'deleteLinkage') {
                        $this->deleteMethod = null;
                    } else {
                        $tableName = $this->getTableName();
                        throw new InvalidParamException("定制自定义删除方法适，请确认 `{$tableName}` 中有 `{$this->activeField}` 字段！");
                    }
                }
            }
        } else {
            // 未设置模型时，将禁止排序
            $this->dragSort = false;
        }
    }

    /**
     * 初始化数据
     */
    protected function initData()
    {
        if ($this->dragSort === true && $this->isSort !== true) {
            throw new InvalidParamException("当设置 [dragSort=true] 时，必须设置 [isSort=true]！");
        }
        if ($this->isSort === true) {
            $this->query->orderBy($this->sort);
        }
        if ($this->deleteMethod) {
            $this->query->andWhere("{$this->activeField} = {$this->activeValue}");
        }
        try {
            $this->data = $this->query->asArray()->all();
        } catch (\yii\db\Exception $e) {
            // 如果表中不存在 sort 字段
            if (preg_match('/Unknown column \'' . $this->sort . '\' in \'order clause/', $e->getMessage())) {
                $tableName = $this->getTableName();
                $sql = "ALTER TABLE `{$tableName}` ADD COLUMN `{$this->sort}` int NULL DEFAULT 0;";
                throw new InvalidParamException("表中必须含有 {$this->sort} 字段并且在结果集中必须包含该字段！可以通过在对应数据库中执行如下 SQL 语句创建该字段：\n{$sql}");
            } else {
                throw new \yii\db\Exception($e->getMessage());
            }
        }
        // 数据总数
        $this->count = $this->query->count();
        // 使用 $pidMapData 来循环输出数据
        foreach ($this->data as $row) {
            $this->pidMapData[$row[$this->pid]][] = $row;
            $idList[$row[$this->key]] = 1;
        }
        // 根据当前筛选出来的数据，得出当前最顶级父元素的 pid 值
        if (empty($idList)) {
            $this->topParentIds[] = 0;
        } else {
            $this->topParentIds = array_keys(array_diff_key($this->pidMapData, $idList));
        }
        // 检查是否需要初始化 `sort` 和 `code` 字段
        if ($this->data) {
            $firstRow = $this->data[0];
            // 如果 sort 值不存在，则初始化
            if ($this->isSort === true && array_key_exists($this->sort, $firstRow) && !$firstRow[$this->sort]) {
                $this->initSort();
            }
            // 如果 code 值不存在，则初始化
            if (array_key_exists($this->code, $firstRow) && !$firstRow[$this->code]) {
                $this->initCode();
            }
        }
    }

    /**
     * 初始化各字段配置，并进行检查
     */
    protected function initColumns()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        // 整理配置参数
        $columns = ArrayHelper::resetOptions($this->columns, ['key' => 'field', 'value' => 'header', 'callback' => 'value']);
        foreach ($columns as $index => $value) {
            if (empty($columns[$index]['field'])) {
                $columns[$index]['field'] = '';
            }
            // 配置项options初始化
            if (empty($columns[$index]['options'])) {
                $columns[$index]['options'] = [];
            }
            // 当没有header属性时，如果设置了 $model ，则尝试从关联模型中获取
            $headerParam = ArrayHelper::getValue($value, 'header');
            if ($headerParam === null) {
                if ($this->model === null) {
                    throw new InvalidParamException("没有设置 [model] 属性时，必须设置列标题 [header] 属性！");
                } else {
                    // 从当前模型中获取属性标签
                    $labels = $this->model->attributeLabels();
                    // 获取字段名
                    $field = $columns[$index]['field'];

                    if (isset($labels[$field])) {
                        $columns[$index]['header'] = $labels[$field];
                    } else {
                        $columns[$index]['header'] = '';
                    }
                }
            } else {
                $field = $columns[$index]['field'];
            }
        }
        $this->columns = $columns;
        if ($this->model) {
            $actions = ['add', 'addChild', 'delete'];
            $actions = array_diff($actions, $this->forbiddenActions);
            $this->columns[] = ['header' => $this->actionHeader, 'type' => $actions, 'options' => []];
        }
    }

    /**
     * 初始化 sort 字段
     */
    public function initSort()
    {
        $tableName = current($this->query->from);
        // 先生成 sort 字段的值
        $this->generateSort($this->topParentIds);
        // 修改所有行的 sort
        foreach ($this->data as $row) {
            self::dbUpdate($tableName, [$this->sort => $row[$this->sort]], $this->key . '=' . $row[$this->key]);
        }
    }

    /**
     * 递归生成 sort
     */
    protected function generateSort($pids = [], $valueMap = [])
    {
        foreach ($this->data as &$row) {
            if (in_array($row[$this->pid], $pids)) {
                $pid = $row[$this->pid];
                $valueMap[$pid] = isset($valueMap[$pid]) ? $valueMap[$pid] + 1 : 1;
                $row[$this->sort] = $valueMap[$pid];
                $this->generateSort([$row[$this->key]], $valueMap);
            }
        }
    }

    /**
     * 初始化 code 字段
     */
    public function initCode()
    {
        $tableName = current($this->query->from);
        // 先生成 code 字段的值
        $this->generateCode($this->topParentIds);
        // 修改所有行的 code
        foreach ($this->data as $row) {
            self::dbUpdate($tableName, [$this->code => $row[$this->code]], $this->key . '=' . $row[$this->key]);
        }
    }

    /**
     * 递归生成 code
     */
    protected function generateCode($pids = [], $valueMap = [], $parentCode = '')
    {
        $currentCode = $parentCode;
        foreach ($this->data as &$row) {
            if (in_array($row[$this->pid], $pids)) {
                $pid = $row[$this->pid];
                $valueMap[$pid] = isset($valueMap[$pid]) ? $valueMap[$pid] + 1 : 1;
                $parentCode = $currentCode === '' ? $valueMap[$pid] : $currentCode . self::$delimiter . $valueMap[$pid];
                $row[$this->code] = $parentCode;
                $this->generateCode([$row[$this->key]], $valueMap, $parentCode);
            }
        }
    }

    /**
     * Run the Widget.
     */
    public function run()
    {
        if ($this->count > 0) {
            $content = preg_replace_callback("/{\\w+}/", function ($matches) {
                return $this->renderSection($matches[0]);
            }, $this->layout);
        } else {
            $content = $this->renderEmpty();
        }

        $this->registerAsset();

        return Html::tag('div', $content, $this->containerOptions);
    }

    /**
     * 注册静态资源
     */
    protected function registerAsset()
    {
        $view = $this->getView();

        $depends = ['common\assets\JqueryFormAsset'];

        if ($this->dragSort === true) {
            $depends[] = 'common\assets\SortableAsset';
        }

        Yii::$container->set('common\widgets\assets\LinkageAsset', [
            'depends' => $depends
        ]);

        \common\widgets\assets\LinkageAsset::register($view);
    }

    /**
     * 渲染各模块
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{summary}':
                return $this->renderSummary();
            case '{items}':
                return $this->renderItems();
            default:
                if (array_key_exists($name, $this->layoutCallback) && is_callable($this->layoutCallback[$name])) {
                    return call_user_func($this->layoutCallback[$name]);
                } else {
                    return '';
                }
        }
    }

    /**
     * 渲染统计模块
     */
    protected function renderSummary()
    {
        if ($this->count <= 0) {
            return '';
        }

        $tag = ArrayHelper::remove($this->summaryOptions, 'tag', 'div');
        
        return Html::tag($tag, '数据总数：' . $this->count . ' 条', $this->summaryOptions);
    }

    /**
     * 渲染主模块中各子项
     */
    protected function renderItems()
    {
        $content = [
            $this->renderHeader(),
            $this->renderUl($this->topParentIds),
            $this->renderHidden()
        ];

        return implode("\n", $content);
    }

    /**
     * 递归渲染列表
     */
    protected function renderUl($pids = [], $recursionLevel = 0)
    {
        $recursionLevel++;
        foreach ($pids as $pid) {
            foreach ($this->pidMapData[$pid] as $row) {
                // 获取行信息
                $content = $this->renderRow($row, $recursionLevel);
                // 如果当前行是父级，则递归
                if ($this->isParentRow($row) === true) {
                    $content .= $this->renderUl([$row[$this->key]], $recursionLevel);
                }
                $items[] = $content;
            }
        }

        return Html::ul($items, $this->ulOptions);
    }

    /**
     * 渲染主体中的行
     */
    public function renderRow($row, $recursionLevel)
    {
        // 获取主键的值
        $key = $this->getKeyValue($row);
        $cols = [];
        $index = 0;
        foreach ($this->columns as $options) {
            $pid = $row[$this->pid];
            // 先判断是否具有[value]属性
            if (!empty($options['value'])) {
                if (is_callable($options['value'])) {
                    // 列内容的回调定制
                    $content = call_user_func($options['value'], $row, $key);
                } else {
                    throw new InvalidParamException('配置项 value 必须设置成可被回调的类型！');
                }
            } elseif (!empty($options['field'])) {
                // 如果没有value属性，则直接获取数据
                $content = $row[$options['field']];
            } else {
                $content = '';
            }

            if ($options['options'] instanceof \Closure) {
                $colOptions = call_user_func($options['options'], $row, $key);
            } else {
                $colOptions = $options['options'];
            }
            $colOptions['data-field'] = ArrayHelper::getValue($options, 'field');
            // 对配置参数[type]属性的处理
            if (($type = ArrayHelper::getValue($options, 'type')) !== null) {
                if (!$this->model && is_string($type) && $type !== 'checkbox') {
                    throw new InvalidParamException('未设置模型的情形下，[type] 属性只能设置成 `checkbox`');
                }
                switch ($type) {
                    case 'checkbox':
                        $content = Html::checkbox('selection[]', false, ['value' => $key]) . $content;
                        $content = Html::tag('label', $content);
                        break;
                    case 'select':
                        $colOptions['data-action'] = 'selectUpdate';
                        break;
                    case 'text':
                        $colOptions['data-action'] = 'textUpdate';
                        break;
                    case 'toggle':
                        $colOptions['data-action'] = 'toggleUpdate';
                        $class = $content == $this->activeValue ? 'linkage-yes' : 'linkage-no';
                        $content = Html::tag('span', '', ['class' => $class]);
                        break;
                    default:
                        // 操作栏的设置
                        if (is_array($type)) {
                            $actionContent = [];
                            // 整理配置参数
                            $actionOptions = ArrayHelper::resetOptions($type, ['key' => 'action', 'value' => 'link']);
                            foreach ($actionOptions as $option) {
                                $link = ArrayHelper::getValue($option, 'link');
                                if (is_callable($link)) {
                                    $link = call_user_func($link, $value, $key);
                                } else {
                                    $link = $link ?: $option['action'];
                                }
                                if (!is_array($link)) {
                                    $href = [$link, 'id' => $key];
                                } else {
                                    $href = $link;
                                }
                                // 暂无自定义默认操作链接的需求
                                switch ($option['action']) {
                                    case 'add':
                                        $actionContent[] = Html::tag('a', '添加同辈', ['href' => 'javascript:;', 'class' => 'linkage-add-link', 'data-pid' => $pid]);
                                        break;
                                    case 'addChild':
                                        if ($recursionLevel >= $this->maxLevel) {
                                            break;
                                        }
                                        $actionContent[] = Html::tag('a', '追加子类', ['href' => 'javascript:;', 'class' => 'linkage-add-link', 'data-pid' => $key]);
                                        break;
                                    case 'delete':
                                        $actionContent[] = Html::tag('a', '删除', ['href' => 'javascript:;', 'class' => 'linkage-delete-link', 'data-key' => $key]);
                                        break;
                                }
                            }
                            $actionContent[] = $content;
                            $content = implode('&nbsp;', $actionContent);
                        }
                        break;
                }
            }

            // 第一列增加缩进
            if ($index === 0) {
                // 验证首行，[type] 只能设置成 `checkbox`
                if ($type && $type !== 'checkbox') {
                    throw new InvalidParamException('第一列的 [type] 属性只能设置成 `checkbox`！');
                }
                // 如果是顶级元素，则不需要前导图片
                if (in_array($pid, $this->topParentIds)) {
                    $prefixAttr = [];
                } else {
                    $prefixAttr = [
                        'class' => 'linkage-fork',
                        'style' => 'margin-left:' . ($recursionLevel - 2 <= 0 ? 0 : $recursionLevel - 2) * 1.5 . 'em;'
                    ];
                }
                if ($this->isParentRow($row) === true) {
                    $tagAttr = ['class' => 'linkage-minus'];
                } else {
                    $tagAttr = ['class' => 'linkage-arrow'];
                }
                $content = Html::tag('span', '', $prefixAttr) . Html::tag('span', '', $tagAttr) . Html::tag('span', $content, $colOptions);

                $cols[] = Html::tag('p', $content, $this->dragSort === true ? ['class' => 'linkage-drag-handle'] : []);
            } else {
                $cols[] = Html::tag('p', $content, $colOptions);
            }
            $index++;
        }

        if ($this->rowOptions instanceof \Closure) {
            $rowOptions = call_user_func($this->rowOptions, $row, $key);
        } else {
            $rowOptions = $this->rowOptions;
        }
        $rowOptions['data-pid'] = $pid;
        // 设置主键值到标签属性中
        $rowOptions['data-key'] = is_array($key) ? json_encode($key) : (string) $key;

        return Html::tag('div', implode('', $cols), $rowOptions);
    }

    /**
     * 渲染头部行
     */
    protected function renderHeader()
    {
        foreach ($this->columns as $options) {
            $items[] = $this->renderHeaderItems($options);
        }

        return Html::tag('div', implode("\n", $items), $this->headerOptions);
    }

    /**
     * 渲染头部列
     */
    protected function renderHeaderItems($options)
    {
        $content = $options['header'];
        $headerColOptions = $this->headerColOptions;
        // 如果字段栏目设置中含有items配置项，则存放在列标题中的标签中
        if (ArrayHelper::getValue($options, 'type') === 'select') {
            $itemsOptions = ArrayHelper::getValue($options, 'items');
            if (is_callable($itemsOptions)) {
                $items = call_user_func($itemsOptions, $content);
            } elseif ($this->model) {
                $model = $this->model;
                $field = $options['field'];
                $method = $this->getMapMethod($field);
                if (method_exists($model::className(), $method)) {
                    $items = call_user_func([$model::className(), $method], $content);
                }
            }
            // 将下拉修改待选内容添加至头部列标签属性中
            if (!empty($items)) {
                $headerColOptions['data-items'] = json_encode($items);
            } else {
                $extraInfo = $this->model ? "或定义 {$model::className()}::$method() 方法" : '';
                throw new InvalidParamException("列 `$field` 设置了 ['type' => 'select'] 属性，则必须设置 [items] 属性" . $extraInfo);
            }
        } elseif (($type = ArrayHelper::getValue($options, 'type')) === 'checkbox') {
            // 如果字段栏设置中配置项type设置为checkbox，则添加checkbox到列标题中
            $content = Html::checkbox('selection_all') . $content;
            $content = Html::tag('label', $content);
        }

        return Html::tag('p', $content, $headerColOptions);
    }

    /**
     * 渲染隐藏域
     */
    protected function renderHidden()
    {
        $hiddens = [
            'model' => $this->query->modelClass,
            'pid' => $this->pid,
            'key' => $this->key,
            'sort' => $this->sort,
            'code' => $this->code,
        ];
        if ($this->beforeAdd) {
            $hiddens['beforeAdd'] = $this->beforeAdd;
        }
        if ($this->deleteMethod) {
            $hiddens['deleteMethod'] = $this->deleteMethod;
        }
        $urls = [
            'ajax-update' => self::createUrl(['ajax-update']),
            'sort-linkage-item' => self::createUrl(['sort-linkage-item']),
            'delete-linkage-item' => self::createUrl(['delete-linkage-item']),
            'add-linkage-item' => self::createUrl(['add-linkage-item']),
            'toggle-linkage-item' => self::createUrl(['toggle-linkage-item']),
        ];

        $items = [
            Html::hiddenInput('', Security::base64encrypt(serialize($hiddens)), ['class' => 'linkageParams']),
            Html::tag('div', 'Now Loading...', ['class' => 'linkage-action-tip'])
        ];
        foreach ($urls as $key => $url) {
            $items[] = Html::hiddenInput('', $url, ['class' => 'url-' . $key]);
        }

        return implode("\n", $items);
    }

    /**
     * 渲染无数据时的内容
     */
    protected function renderEmpty()
    {
        $tag = ArrayHelper::remove($this->emptyTextOptions, 'tag', 'div');

        return Html::tag($tag, $this->emptyText, $this->emptyTextOptions);
    }

    /**
     * 该方法将会根据模型中的字段来设置要展示的列
     */
    protected function guessColumns()
    {
        if ($this->model !== null) {
            foreach ($this->model->attributes as $field => $value) {
                $this->columns[] = $field;
            }
        }
    }

    /**
     * 获取字段对应的映射方法的名称
     * 
     * @param  string $field 字段名
     * @return string        对应方法的名称
     */
    protected function getMapMethod($field)
    {
        return 'get' . Inflector::camelize($field) . 'Map';
    }

    /**
     * 获取字段对应的重置方法的名称
     * 
     * @param  string $field 字段名
     * @return string        重置方法的名称
     */
    protected function getValueMethod($field)
    {
        return 'get' . Inflector::camelize($field) . 'Value';
    }

    /**
     * 获取模型对应的完整表名
     * 
     * @return string 完整表名
     */
    protected function getTableName()
    {
        return preg_replace('/{{%(.*)}}/Ui', Yii::$app->db->tablePrefix . '$1', current($this->query->from));
    }

    /**
     * 从每行数据中，获取主键的值
     * 
     * @param  array|object $row 循环中的每行数据
     * @return string            主键的值
     */
    protected function getKeyValue($row)
    {
        return isset($row[$this->key]) ? $row[$this->key] : null;
    }

    /**
     * 判断当前行是否还有子元素
     */
    protected function isParentRow($row)
    {
        return isset($this->pidMapData[$row[$this->key]]);
    }

    /**
     * 删除元素方法
     */
    public static function deleteItem()
    {
        $key = Yii::$app->request->post('key');
        $params = unserialize(Security::base64decrypt(Yii::$app->request->post('params')));
        $className = $params['model'];
        $keyField = $params['key'];
        $pidField = $params['pid'];
        $deleteMethod = ArrayHelper::getValue($params, 'deleteMethod');
        // 寻找该节点的所有子集，并一起删除
        $getChildrenId = function ($key) use ($className, $keyField, $pidField, &$getChildrenId) {
            $map = $className::find()
                ->where($pidField . ' = :key', [':key' => $key])
                ->map($keyField, $keyField);
            $childIds = [];
            foreach ($map as $id) {
                $childIds = array_merge($getChildrenId($id), $childIds);
                $childIds[] = $id;
            }
            return $childIds;
        };
        $ids = $getChildrenId($key);
        $ids[] = $key;
        $model = new $className;
        if (method_exists($model, $deleteMethod)) {
            return call_user_func([$model, $deleteMethod], $ids);
        }
        if (in_array('state', $model->attributes())) {
            $updateMap = ['state' => $model::STATE_INVALID];
            if (in_array('updated_at', $model->attributes())) {
                $updateMap['updated_at'] = self::$time;
            }
            if (in_array('updated_by', $model->attributes())) {
                $updateMap['updated_by'] = u('id');
            }
            $ret = $model::updateAll($updateMap, [$key => $list]);
        } else {
            $ret = $model::deleteAll([$key => $list]);
        }
        if ($ret) {
            return true;
        } else {
            return '已经删除了！';
        }
    }

    /**
     * 添加元素方法
     */
    public static function addItem()
    {
        $pid = Yii::$app->request->post('pid');
        $params = unserialize(Security::base64decrypt(Yii::$app->request->post('params')));
        $className = $params['model'];
        $sortField = $params['sort'];
        $codeField = $params['code'];
        $pidField = $params['pid'];
        $beforeAdd = ArrayHelper::getValue($params, 'beforeAdd');

        $model = new $className;
        $model->attributes = Yii::$app->request->post('Linkage');
        $model->{$pidField} = $pid;
        if (method_exists($model, $beforeAdd)) {
            call_user_func([$model, $beforeAdd]);
        }
        if ($model->insert()) {
            // 设置 `sort` 字段
            $lastChild = $className::find()
                ->select($sortField)
                ->where($pidField . ' = :pid', [':pid' => $pid])
                ->orderBy([$sortField => SORT_DESC])
                ->asArray()
                ->one();
            $model->{$sortField} = (string) ($lastChild[$sortField] + 1);
            // 如果存在 `code` 字段则进行设置 
            if (in_array($codeField, $model->attributes()) && !$model->{$codeField}) {
                $keyField = current($model->primaryKey());
                $lastChild = $className::find()
                    ->where($pidField . ' = :pid AND ' . $keyField . ' <> :key', [':pid' => $pid, ':key' => $model->{$keyField}])
                    ->orderBy([$keyField => SORT_DESC])
                    ->one();
                if ($pid == 0) {
                    $model->{$codeField} = (string) ($lastChild[$codeField] + 1);
                } else {
                    $parent = $className::findOne($pid);
                    $lastCodeValue = substr($lastChild[$codeField], strrpos($lastChild[$codeField], self::$delimiter) + 1);
                    $model->{$codeField} = $parent[$codeField] . self::$delimiter . ($lastCodeValue + 1);
                }
            }
            $model->update();
            $keyField = current($model->primaryKey());
            return [true, ['data' => $model->attributes, 'key' => $model->{$keyField}]];
        } else {
            return [false, $model->getErrors()];
        }
    }

    /**
     * 排序方法
     */
    public static function sortItem()
    {
        $sortList = Yii::$app->request->post('list');
        $params = unserialize(Security::base64decrypt(Yii::$app->request->post('params')));
        $className = $params['model'];
        $sortField = $params['sort'];
        $keyField = $params['key'];

        $data = $className::find()
            ->where(['in', $keyField, $sortList])
            ->all();

        $keySortMap = array_flip($sortList);
        $successNum = 0;
        foreach ($data as $model) {
            $model->{$sortField} = $keySortMap[$model->{$keyField}] + 1;
            if ($model->update()) {
                $successNum++;
            }
        }
        return $successNum;
    }
}
