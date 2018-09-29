<?php

namespace common\widgets;

use Yii;
use common\helpers\Hui;
use common\helpers\Html;
use common\helpers\Excel;
use common\helpers\Inflector;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use yii\base\InvalidParamException;

/**
 * 该组件的作用主要是以表格的形式快速呈现数据.
 * 如下是一个基本的应用方式：
 *
 * ```php
 * $query = User::find();
 * $html = Table::widget([
 *     'query' => $query,
 *     'columns' => [
 *         ['type' => 'checkbox'],
 *         'id',
 *         'user.name' => ['sort' => true, 'value' => function ($value, $key) {}, 'search' => ['header' => '名称']],
 *         'state' => ['type' => 'select', 'items' => ['User', 'getStateMap'], 'search' => true],
 *         ['type' => ['edit' => 'edit-one', 'view', 'delete', 'reset']]
 *         // ...
 *     ],
 *     'searchColumns' => [
 *         'id',
 *         'name' => 'select', //指定搜索框类型，select表示下拉型，如果存在 $modelClass, 则会去对应的模型中寻找 get{field}Map()
 *         'email' => ['header' => 'EMAIL', 'type' => 'radio', 'items' => [$object, 'mapMethod']]
 *         // ...
 *     ]
 * ])
 * ```
 * 更多场景中，是使用 yii\db\Query 对象来进行查询，为方便更多情况的应用，该组件已集成进 common\traits\QueryTrait 中，以下是一个集成后的使用例子：
 * 
 * ```php
 * $query = User::find();
 * $html = $query->getTable([
 *     ['type' => 'checkbox'],
 *         'id',
 *         'user.name' => ['sort' => true, 'value' => function ($value, $key) {}, 'search' => ['header' => '名称']],
 *         'state' => ['type' => 'select', 'items' => ['User', 'getStateMap'], 'search' => true],
 *         ['type' => ['edit' => 'edit-one', 'view', 'delete', 'reset']]
 *         // ...
 *     ], [
 *     'searchColumns' => [
 *         'id',
 *         'name' => 'select', //指定搜索框类型，select表示下拉型，如果存在 $modelClass, 则会去对应的模型中寻找 get{field}Map()
 *         'email' => ['header' => 'EMAIL', 'type' => 'radio', 'items' => [$object, 'mapMethod']]
 *         // ...
 *     ]
 * ]);
 * ```
 * 
 * 必须传入的参数：$query 或 $data, 优先考虑传入 $query
 *
 * 其他主要参数说明：
 * 1.$modelClass or $model（当传入 $query 时，将会对这2个参数自动赋值）:
 * （1）可以获取到所有的关联表信息
 * （2）字段的格式化方法将会从对应模型中去寻找 get{$field}Value() 类型的方法
 * （3）表格列标题将会自动从对应模型中 attributeLabels() 去寻找
 * （4）自动识别主键字段，否则主键字段默认为id，根据实际情况必须做出手动传参调整
 * 2.$columns: 对列表显示的列进行设置，如果不传入将会获取 $query 的 model 的所有属性或 $data 的所有列
 * （1）当设置了 `type => 'select'`, 可以设置 `items => `'string'`|`callable`, 来设置下拉型修改的内容
 * （2）当设置了 `type => ['edit', 'view', ...]`, 表示设置了操作栏，内置了如下操作：'edit', 'view', 'delete', 'reset'
 *     'edit': 在操作栏中会添加一个编辑按钮，点击会出现弹窗，弹窗的链接默认为当前控制器的 actionEdit()
 *     'view': 在操作栏中会添加一个查看按钮，点击会出现弹窗，弹窗的链接默认为当前控制器的 actionView()
 *     'delete': 在操作栏中会添加一个删除按钮，点击会出现一个确认删除框
 *     'reset': 表示列表的列设置功能
 * （3） 当设置了 `search => true|[options], 可以直接快捷创建搜索框
 * 3.$searchColumns: 如果设置了该项，将会出现搜索栏
 * 4.$count（当传入 $query 时，该项将会自动赋值）: 会输出该列表的总计条目数
 * 5.$key: 当传入 $query 时，将会自动判断主键，当传入 $data 时，默认为 id，需手动传值来改变
 *
 * 新特性：
 * 1.静态资源的自动加载机制，如js、css、image将在使用到的情况下将自行载入
 * 2.规范自定义的配置，现在通过可回调的参数类型，达到动态配置的效果
 * 3.整个组件可通过模板自行调整布局
 * 4.模型关系的自动关联，现在将会自动寻找关联关系，获取各模型的标签和回调方法
 * 5.操作栏不再是内置的了，而是像普通的栏位一样可以被配置
 *
 * 当使用 $query 时，请参考以下建议：
 * 1.当需要显示其他表的字段的时，如果 $query 调用了 with() 或 joinWith()，则 $columns 配置时可以直接使用 `alias`.`field` 格式进行输出
 * 2.为正确使用排序功能，则当传入的是 $query 时，则必须使用 joinWith() 进行表的关联
 * 3.为更好使用搜索功能，请确保 $query 是通过 \common\components\ARModel::search() 获得的，因为这将自动处理大部分的搜索条件设置
 *
 * 当使用 $data 时，以下几点需要注意：
 * 1.所有列的名字不能使用别名，只能直接使用字段名
 * 2.如果不设置 $modelClass, 诸如单元格的快捷修改等功能无法使用
 *
 * 其他注意事项：
 * 1.开发时不建议直接开启列设置功能，因为有可能在调整显示的列时出现错乱现象
 * 2.当启用列设置功能时，建议字段栏的设置中，`type => 'checkbox'` 放在第一列，`type => []` 放在最后一列，否则可能导致该两列在设置会消失的情况
 * 
 * @author ChisWill
 */
class Table extends \yii\base\Widget
{
    use \common\traits\ChisWill;

    /**
     * @var yii\db\ActiveQuery|yii\db\Query $query 和 $data, 两者至少有一个得有值
     */
    public $query = null;
    /**
     * @var array 一般是从数据库中的数据，当设置该项时，会忽略从$query中取数
     */
    public $data = null;
    /**
     * @var yii\db\ActiveRecord
     */
    public $model = null;
    /**
     * @var integer 数据总量
     */
    public $count = 0;
    /**
     * @var string 主键字段，默认为id
     */
    public $key = 'id';
    /**
     * @var array 列表每个字段的配置
     */
    public $columns = [];
    /**
     * @var array 搜索栏每个字段的配置
     */
    public $searchColumns = [];
    /**
     * @var boolean 是否开启列设置功能
     */
    public $sortColumns = false;
    /**
     * @var boolean 是否是Ajax形式翻页、搜索、排序
     */
    public $isAjax = true;
    /**
     * @var boolean 是否开启排序
     */
    public $isSort = true;
    /**
     * @var boolean 是否默认开启搜索
     */
    public $isSearch = false;
    /**
     * @var boolean|string 是否导出Excel
     * Excel文件名优先取该配置的字符串值
     * 未设置时取当前页面的title值
     */
    public $export = false;
    /**
     * @var boolean|array 是否开启批量删除，只有当配置过 ['type' => 'checkbox'] 时才生效
     * 设置成true，将会请求当前controller的actionDeleteAll
     * 或者直接设置数组格式的url参数
     */
    public $deleteAllBtn = false;
    /**
     * @var false|integer|array
     * - `false`: 禁用分页
     * - `integer`: 开启分页，并设置每页个数
     * - `array`: 开启分页，并可以设置更丰富的配置，数组形式如下所示
     * ```php
     * [
     *     'pageSize' => 10,
     *     'pageParam' => 'page'
     * ]
     * ```
     */
    public $paging = PAGE_SIZE;
    /**
     * @var string 搜索图片的链接，如果不填将会启用默认图片
     */
    public $loadingImgSrc = null;
    /**
     * @var boolean whether to show the header section of the grid table.
     */
    public $showHeader = true;
    /**
     * @var boolean whether to show the footer section of the grid table.
     */
    public $showFooter = false;
    /**
     * @var integer 最大显示的列数，不包含操作栏
     */
    public $maxColumnNum = 17;
    /**
     * @var string 快速更新的请求的action
     */
    public $ajaxUpdateAction = 'ajaxUpdate';
    /**
     * @var array 排序的配置项信息
     */
    public $sortOptions = ['sortParam' => 'sort'];
    /**
     * @var string 无数据时的显示文本信息
     */
    public $emptyText = '没有数据';
    /**
     * @var array the HTML attributes for the emptyText of the list view.
     * The "tag" element specifies the tag name of the emptyText element and defaults to "div".
     */
    public $emptyTextOption = ['class' => 'empty'];
    /**
     * @var array the HTML attributes for the container tag of the list view.
     * The "tag" element specifies the tag name of the container element and defaults to "div".
     */
    public $options = ['class' => 'list-container cl'];
    /**
     * @var array the HTML attributes for the search bar of the list view.
     */
    public $searchOptions = ['class' => 'search'];
    /**
     * @var array the HTML attributes for the grid table element.
     */
    public $tableOptions = ['class' => 'table'];
    /**
     * @var array the HTML attributes for the summary of the list view.
     * The "tag" element specifies the tag name of the summary element and defaults to "div".
     */
    public $summaryOptions = ['class' => 'summary'];
    /**
     * @var array the HTML attributes for the table header row.
     */
    public $headerRowOptions = [];
    /**
     * @var array the HTML attributes for the header cell tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $headerOptions = [];
    /**
     * @var array|Closure the HTML attributes for the table body rows. This can be either an array
     * specifying the common HTML attributes for all body rows, or an anonymous function that
     * returns an array of the HTML attributes. The anonymous function will be called once.
     */
    public $rowOptions = [];
    /**
     * @var Closure an anonymous function that is called once BEFORE rendering each data model.
     * It should have the similar signature as [[rowOptions]]. The return result of the function
     * will be rendered directly.
     */
    public $beforeRow;
    /**
     * @var Closure an anonymous function that is called once AFTER rendering each data model.
     * It should have the similar signature as [[rowOptions]]. The return result of the function
     * will be rendered directly.
     */
    public $afterRow;
    /**
     * @var callable 渲染搜索栏各项时执行
     */
    public $beforeSearchRow;
    /**
     * @var boolean 是否显示数据总数
     */
    public $showCount = true;
    /**
     * @var string 操作栏的标题名称
     */
    public $operationTitle = '操作';
    /**
     * @var array 弹窗显示的按钮组，主要用于添加该列表新元素，格式：['url' => '按钮描述']
     */
    public $addBtn = [];
    /**
     * @var array 额外按钮组，格式：['url' => '按钮描述']
     */
    public $extraBtn = [];
    /**
     * @var array ajax返回的额外内容
     */
    public $ajaxReturn = [];
    /**
     * @var string Ajax请求时的模板，以下都是内置标签块
     * 
     * - `{search}`: the search section. See [[renderSearch()]].
     * - `{summary}`: the summary section. See [[renderSummary()]].
     * - `{items}`: the list items. See [[renderItems()]].
     * - `{paging}`: the paging section. See [[renderPaging()]].
     */
    public $ajaxLayout = "{summary}\n{items}\n{paging}";
    /**
     * @var string 非Ajax请求时的模版，可以通过修改模版，设置自定义的标签块，来定制个性化的布局
     */
    public $layout = "{search}\n<div class=\"list-view\">{ajaxLayout}</div>";
    /**
     * @var array 自定义标签块的回调方法，键值对的形式，eg: ['{calendar}' => function () {return 'something';}]
     */
    public $layoutCallback = [];

    /************************* 以下属性均为程序运算需要，不要尝试从外部进行复制 *************************/
    /**
     * @var string 当前表格的ID
     */
    protected $tableId;
    /**
     * @var string 当前请求的action
     */
    protected $action;
    /**
     * @var string yii\db\ActiveRecord的类名
     */
    protected $modelClass = null;
    /**
     * @var array 记录模型中拥有的回调函数
     */
    protected $modelCallback = [];
    /**
     * @var array 记录 yii\db\ActiveQuery 中的with信息
     */
    protected $queryWith = [];
    /**
     * @var array 记录所有关联表的模型
     */
    protected $withModel = [];
    /**
     * @var array 记录每个字段的header值
     */
    protected $fieldHeaderMap = [];
    /**
     * @var array 配置栏的初始设定
     */
    protected $originColumns = [];
    /**
     * @var string 请求中表格id的参数名
     */
    protected $tableIdParamName = 'tableid';
    /**
     * @var string 列设置的缓存名
     */
    protected $resetColumnsCacheName = 'tableResetColumns';
    /**
     * @var integer 当前请求执行的该插件个数
     */
    private static $currentTableCount = 0;
    /**
     * @var boolean 是否配置了复选框一栏
     */
    private $hasCheckbox = false;

    public function init()
    {
        parent::init();
        // 计数器递增
        self::$currentTableCount++;
        // 设置表格id
        if ($this->tableId === null) {
            $this->tableId = self::$currentTableCount;
        }
        // 判断是否跳过之后的初始化处理
        if ($this->isSkipRun()) {
            return;
        }
        // 初始化多表格时的场景配置
        $this->initMulitSetting();
        // 如果 $data 存在，则当 $query 不存在时，尝试给 $query 赋值
        if ($this->data !== null) {
            // 如果 $query 不存在，则尝试给 $query 赋值
            if ($this->query === null) {
                $this->guessQuery();
                // 如果开启排序则进行设置
                if ($this->isSort === true) {
                    $this->setSort();
                }
            }
            $this->count = count($this->data);
        } elseif ($this->query !== null) { // 否则如果 $query 不存在，则进行查询获取数据
            // 如果开启排序则进行设置
            if ($this->isSort === true) {
                $this->setSort();
            }
            // 如果开启了分页，则使用分页查询方式
            if ($this->paging !== false && !get('exportExcel')) {
                $pageSize = is_array($this->paging) ? $this->paging['pageSize'] : $this->paging;
                $this->data = $this->query->paginate($pageSize);
                $this->count = $this->query->totalCount;
            } else { // 否则使用普通查询方式
                $this->data = $this->query->all();
                $this->count = $this->query->count();
            }
        } else {
            throw new InvalidParamException('比如传入 $data 或 $query 中的一个作为显示列表的最基本的参数！');
        }

        // 共通性的整理
        if (is_string($this->model)) {
            $this->model = new $this->model;
        } elseif ($this->model === null && $this->query instanceof \yii\db\ActiveQuery) {
            $this->model = new $this->query->modelClass;
        }
        if ($this->model) {
            $this->modelClass = get_class($this->model);
            // 设置本表主键字段
            if (method_exists($this->model, 'primaryKey')) {
                $this->key = $this->model->primaryKey();
            }
        }
        // $query存在时，设置$queryWith
        if ($this->query !== null && $this->query instanceof \yii\db\ActiveQuery && is_array($this->query->with)) {
            $this->queryWith = array_unique($this->query->with);
        }
        // 将主键的配置强制转为数组形式
        is_string($this->key) && $this->key = (array) $this->key;

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        $controller = Yii::$app->controller;
        $this->action = $controller->id . '/' . $controller->action->id;
        // 初始化列设置的缓存名
        $this->setResetColumnsCacheName();
        // 初始化字段配置
        $this->initColumns();
        // 先查找所有字段可能的回调函数
        $this->setModelCallback();
    }

    /**
     * 多次调用本组件时的配置
     */
    protected function initMulitSetting()
    {
        if (self::$currentTableCount > 1) {
            $urlParams = array_merge(req()->getQueryParams(), [$this->tableIdParamName => $this->tableId]);
            Yii::$container->set('yii\data\Pagination', [
                'params' => $urlParams,
                'pageParam' => 'p' . self::$currentTableCount
            ]);
            Yii::$container->set('yii\data\Sort', [
                'params' => $urlParams,
            ]);
        }
    }

    /**
     * 清除DI的设置
     */
    protected function clearDefinitions()
    {
        Yii::$container->clear('yii\data\Pagination');
        Yii::$container->clear('yii\data\Sort');
    }

    /**
     * 初始化各字段配置，并进行检查
     */
    protected function initColumns()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        // 获取列设置的配置信息
        $setFieldArr = $this->getCache();
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
            // 对搜索字段的初始化
            if (($searchOptions = ArrayHelper::getValue($columns[$index], 'search', $this->isSearch)) !== false && $columns[$index]['field']) {
                if ($this->getColumnAlias($columns[$index]['field'])) {
                    $searchField = $this->getFullField($columns[$index]['field']);
                } else {
                    $searchField = $columns[$index]['field'];
                }
                if ($searchOptions === true) {
                    $this->searchColumns[] = $searchField;
                } else {
                    $this->searchColumns[$searchField] = $searchOptions;
                }
            }
            if (!empty($value['type']) && !is_array($value['type']) && is_array(current($this->data))) {
                throw new InvalidParamException("数据源是数组时，请不要设置[type]属性！");
            }
            // 检查字段配置参数有效性，如果字段设置中含有点，则必须 $query 必须继承自 yii\db\ActiveQuery, 且设置了对应的with
            if ($withName = $this->getWithName($columns[$index]['field'])) {
                if ($withName !== false) {
                    $this->generateWithModel($withName);
                } else {
                    $alias = explode('.', $columns[$index]['field'])[0];
                    throw new InvalidParamException("请主动加载关联 {$alias}，才能使用 {$columns[$index]['field']} 输出关联表信息！");
                }
            }
            // 当没有header属性时，如果设置了 $model ，则尝试从关联模型中获取
            $headerParam = ArrayHelper::getValue($value, 'header');
            if ($headerParam === null) {
                if (is_array(ArrayHelper::getValue($value, 'type'))) {
                    $columns[$index]['header'] = '';
                } elseif ($this->model === null) {
                    throw new InvalidParamException("当传入参数为数组时，必须设置列标题属性[header]！");
                } else {
                    // 获取字段名
                    $field = $this->getColumnName($columns[$index]['field']);
                    // 从模型中获取label
                    $columns[$index]['header'] = $this->getFieldLabel($columns[$index]['field']);
                    $this->fieldHeaderMap[$field] = $columns[$index]['header'];
                }
            } else {
                $field = $this->getColumnName($columns[$index]['field']);
                $this->fieldHeaderMap[$field] = $value['header'];
            }

            // 保留初始化配置
            $this->originColumns[$index] = $columns[$index];
        }
        // 根据列设置的信息，调整列信息
        $this->columns = [];
        if ($setFieldArr) {
            foreach ($setFieldArr as $field) {
                if (array_key_exists($field, $columns)) {
                    $this->columns[$field] = $columns[$field];
                }
            }
            $firstOption = reset($columns);
            if (ArrayHelper::getValue($firstOption, 'type') === 'checkbox') {
                array_unshift($this->columns, $firstOption);
            }
            $lastOption = end($columns);
            if (is_array(ArrayHelper::getValue($lastOption, 'type'))) {
                array_push($this->columns, $lastOption);
            }
        } else {
            $firstOption = reset($columns);
            if (ArrayHelper::getValue($firstOption, 'type') === 'checkbox') {
                $maxColumnNum = $this->maxColumnNum + 1;
                $this->hasCheckbox = true;
            } else {
                $maxColumnNum = $this->maxColumnNum;
            }
            $this->columns = array_slice($columns, 0, $maxColumnNum);
            if ($maxColumnNum <= count($columns) - 1) {
                $lastOption = end($columns);
                if (is_array(ArrayHelper::getValue($lastOption, 'type'))) {
                    array_push($this->columns, $lastOption);
                }
            }
        }
    }

    /**
     * 执行该Widget
     */
    public function run()
    {
        // 判断是否跳过之后本表格
        if ($this->isSkipRun()) {
            return;
        }
        // 判断是否是导出Excel
        if (get('exportExcel')) {
            $this->exportExcel();
        }
        // 判断是否是列设置保存的的请求
        if (get('saveResetTable')) {
            $this->saveResetTable();
        }
        // 判断是否是列设置的请求
        if (get('resetTable')) {
            $this->renderResetTable();
        }

        if (req()->isAjax) {
            $layout = $this->ajaxLayout;
        } else {
            $layout = str_replace('{ajaxLayout}', $this->ajaxLayout, $this->layout);
        }
        $content = preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderSection($matches[0]);
            return $content === false ? $matches[0] : $content;
        }, $layout);

        // 如果是 Ajax 请求，则直接输出内容
        if (req()->isAjax) {
            $data = [];
            foreach ($this->ajaxReturn as $key => $value) {
                $data[$key] = $value;
            }
            self::success($content, $data);
        }

        $this->clearDefinitions();

        $content .= Html::input('hidden', null, $this->isAjax ? 1 : 0, ['class' => 'isAjax']);

        $this->registerAsset();

        $tag = ArrayHelper::remove($this->options, 'tag', 'div');

        return Html::tag($tag, $content, $this->options);
    }

    /**
     * 根据配置情况，引入需要的资源包
     */
    protected function registerAsset()
    {
        $view = $this->getView();

        $js = [];
        $depends = [];

        $flag = $fancyFlag = $dialogFlag = $sortFlag = false;
        foreach ($this->columns as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            $type = ArrayHelper::getValue($value, 'type');
            if (is_string($type)) {
                $flag = true;
            } elseif (is_array($type)) {
                $type = ArrayHelper::resetOptions($type, ['key' => 'action', 'value' => 'link']);
                foreach ($type as $option) {
                    if ($option['action'] === 'delete') {
                        $dialogFlag = true;
                    }
                    if (in_array($option['action'], ['view', 'edit', 'reset'])) {
                        $fancyFlag = true;
                    }
                }
            }
            $sortFlag = ArrayHelper::getValue($value, 'sort', $this->isSort);
        }

        if ($this->isAjax || $flag) {
            $js[] = 'js/table.js';
            $depends[] = 'common\assets\CommonAsset';
        }

        if ($this->addBtn || $fancyFlag) {
            $depends[] = 'common\assets\FancyBoxAsset';
        }

        if ($dialogFlag) {
            $depends[] = 'common\assets\LayerAsset';
        }

        if ($this->isAjax && $this->searchColumns) {
            $depends[] = 'common\assets\JqueryFormAsset';
        }

        if ($sortFlag) {
            $css[] = 'css/table.css';
        }

        $pickerPluginTypes = ['dateRange', 'timeRange', 'time', 'date', 'datetime'];
        foreach ($this->searchColumns as $key => $value) {
            if (is_string($value) && in_array($value, $pickerPluginTypes) || is_array($value) && in_array(ArrayHelper::getValue($value, 'type'), $pickerPluginTypes)) {
                $depends[] = 'common\assets\TimePickerAsset';
                break;
            }
        }

        Yii::$container->set('common\widgets\assets\TableAsset', [
            'js' => $js,
            'depends' => $depends
        ]);

        \common\widgets\assets\TableAsset::register($view);
    }

    protected function exportExcel()
    {
        $excel = self::createExcel();
        // 设置Sheet
        $sheet = $excel->setActiveSheetIndex(0);
        if (is_string($this->export)) {
            $title = $this->export;
        } else {
            $title = Yii::$app->view->title;
        }
        // 设置Sheet标题
        $sheet->setTitle($title);
        // 设置列标题
        $titles = [];
        foreach ($this->columns as $options) {
            if (!$this->isOperationColumn($options)) {
                $titles[] = $options['header'];
            }
        }
        Excel::setTitles($sheet, $titles);
        foreach ($this->data as $r => $value) {
            $key = $this->getKeyValue($value);
            $contents = [];
            foreach ($this->columns as $options) {
                if (!$this->isOperationColumn($options)) {
                    $contents[] = $this->getContent($value, $key, $options);
                }
            }
            Excel::setContents($sheet, $contents, $r + 2);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . iconv("utf-8", "gb2312", $title) . '.xlsx"');
        header('Cache-Control: max-age=0');

        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');

        exit;
    }

    protected function saveResetTable()
    {
        $resetFields = post('field');
        $isReset = post('isReset');
        
        if ($isReset) {
            $this->setCache([]);
        } else {
            $this->setCache($resetFields);
        }

        self::success();
    }

    protected function renderResetTable()
    {
        // 获取用户的列配置信息
        $setFieldArr = $this->getCache();
        // 初始化原始的列
        $fieldList = '';
        $columnCount = 0;
        foreach ($this->originColumns as $index => $option) {
            if (!$this->isOperationColumn($option)) {
                $columnCount++;
                $fieldList .= Html::tag('p', $option['header'], ['field' => $index]);
            }
        }
        // 左侧标题
        $leftTitle = '该列表共 ' . Html::selfSpan($columnCount) . ' 列（每个列表最多显示 ' . Html::errorSpan($this->maxColumnNum) . ' 列！）：';
        // 初始化已选择的列
        $setFieldList = '';
        foreach ($setFieldArr as $index) {
            if (array_key_exists($index, $this->columns)) {
                $setFieldList .= $index . ',';
            }
        }
        $setFieldList = trim($setFieldList, ',');
        $selectedCount = count($setFieldArr);
        // 右侧标题
        $rightTitle = '已选择 ' . Html::selfSpan($selectedCount, ['id' => 'selectedCount']) . ' 列（可拖拽排序！最终将按此顺序显示）：';
        // 清除全局视图事件
        self::offEvent();
        // 设置表单提交的action
        $action = [$this->action, 'saveResetTable' => true];
        $this->addParamsTableId($action);
        // 渲染视图并输出
        echo $this->render('table/resetTable', compact('fieldList', 'setFieldList', 'leftTitle', 'rightTitle', 'action'));
        // 终止之后的代码执行
        exit;
    }

    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     * @return string|boolean the rendering result of the section, or false if the named section is not supported.
     */
    protected function renderSection($name)
    {
        switch ($name) {
            case '{search}':
                return $this->renderSearch();
            case '{summary}':
                return $this->renderSummary();
            case '{items}':
                return $this->renderItems();
            case '{paging}':
                return $this->renderPaging();
            default:
                if (array_key_exists($name, $this->layoutCallback) && is_callable($this->layoutCallback[$name])) {
                    return call_user_func($this->layoutCallback[$name]);
                } else {
                    return false;
                }
        }
    }

    /**
     * Renders the search bar.
     */
    protected function renderSearch()
    {
        if (req()->isAjax) {
            return '';
        }

        $content = $this->renderSearchItems();

        if (!$content) {
            return '';
        }

        $tag = ArrayHelper::remove($this->searchOptions, 'tag', 'div');
        
        return Html::tag($tag, $content, $this->searchOptions);
    }

    /**
     * Renders the summary text.
     */
    protected function renderSummary()
    {
        $tag = ArrayHelper::remove($this->summaryOptions, 'tag', 'div');
        $btns = [];
        $countHtml = $this->showCount === true ? '数据总数：' . Html::successStrong($this->count) . ' 条' : '';
        if ($this->hasCheckbox === true && $this->deleteAllBtn) {
            $href = is_array($this->deleteAllBtn) ? self::createUrl($this->deleteAllBtn) : self::createUrl(['deleteAll']) ;
            $btns[] = Hui::dangerBtn('批量删除', $href, ['class' => 'deleteAllBtn', 'data-model' => $this->modelClass]);
        }
        if ($this->addBtn) {
            foreach ($this->addBtn as $url => $text) {
                $btns[] = Hui::secondaryBtn($text, [$url], ['class' => 'view-fancybox fancybox.iframe']);
            }
        }
        if ($this->export) {
            $action = [$this->action, 'exportExcel' => true];
            $btns[] = Hui::warningBtn('导出Excel', $action, ['class' => 'exportBtn']);
        }
        if ($this->extraBtn) {
            foreach ($this->extraBtn as $url => $text) {
                $btns[] = Hui::successBtn($text, [$url], ['class' => 'extra-btn']);
            }
        }

        $content = implode('', [
            Html::tag('span', implode('&nbsp;', $btns), ['class' => 'l']),
            Html::tag('span', $countHtml, ['class' => 'r'])
        ]);
        
        return Html::tag($tag, $content, $this->summaryOptions);
    }

    /**
     * Renders the data models for the grid view.
     */
    protected function renderItems()
    {
        $tableHeader = $this->showHeader ? $this->renderTableHeader() : false;
        $tableBody = $this->renderTableBody();
        $tableFooter = $this->showFooter ? $this->renderTableFooter() : false;

        $content = array_filter([
            $tableHeader,
            $tableBody,            
            $tableFooter,
        ]);

        return Html::tag('table', implode("\n", $content), $this->tableOptions);
    }

    /**
     * Renders the search bar items.
     * @return string the rendering result.
     */
    protected function renderSearchItems()
    {
        $cells = [];
        if ($this->model !== null) {
            $labels = $this->model->attributeLabels();
        } else {
            $labels = [];
        }
        // 整理配置参数
        $this->searchColumns = ArrayHelper::resetOptions($this->searchColumns, ['key' => 'field', 'value' => 'type', 'callback' => 'value']);
        foreach ($this->searchColumns as $index => $option) {
            if (is_callable($this->beforeSearchRow)) {
                $option = call_user_func($this->beforeSearchRow, $option, $index);
            }
            $column = $option['field'];
            $field = $this->getColumnName($column);
            if ($withName = $this->getWithName($column)) {
                if ($withName !== false) {
                    $this->generateWithModel($withName);
                } else {
                    $alias = explode('.', $column)[0];
                    throw new InvalidParamException("请主动加载关联 {$alias}，才能使用 {$columns[$index]['field']} 输出关联表信息！");
                }
            }
            if (empty($option['type'])) {
                if (isset($option['value'])) {
                    $option['type'] = 'custom';
                } else {
                    $option['type'] = 'text';
                }
            }
            if (empty($option['options'])) {
                $option['options'] = [];
            }
            $labelField = strpos($field, '_') === 0 ? substr($field, 1) : $field;
            if (!empty($option['header'])) {
                $label = $option['header'];
            } elseif (array_key_exists($labelField, $this->fieldHeaderMap)) {
                $label = $this->fieldHeaderMap[$labelField];
            } else {
                if ($withName) {
                    $label = $this->getFieldLabel($column);
                } elseif (isset($labels[$labelField])) {
                    $label = $labels[$labelField];
                } else {
                    $label = '';
                }
            }
            // name属性前缀
            $namePrefix = 'search';
            $name = "{$namePrefix}[$column]";
            // 获取所有的搜索值
            $searchValue = get($namePrefix);
            $model = null;
            $htmlMethod = null;
            $content = '';
            switch ($option['type']) {
                case 'time':
                case 'date':
                case 'datetime':
                    $classOption = (array) ArrayHelper::remove($option['options'], 'class', []);
                    $classOption[] = str_replace('.', '-', $column) . '-' . $option['type'] . 'picker';
                    $content = Html::input('text', $name, ArrayHelper::getValue($searchValue, $column), array_merge(['placeholder' => $label, 'class' => $classOption], $option['options']));
                    break;
                case 'dateRange':
                    $start = 'start_' . $field;
                    $end = 'end_' . $field;
                    !$label && $label = '日期';
                    $input = [];
                    $classOption = (array) ArrayHelper::remove($option['options'], 'class', []);
                    $input[] = Html::input('text', "{$namePrefix}[$start]", ArrayHelper::getValue($searchValue, $start), array_merge(['placeholder' => '开始' . $label, 'class' => $classOption + [-1 => 'startdate']], $option['options']));
                    $input[] = Html::input('text', "{$namePrefix}[$end]", ArrayHelper::getValue($searchValue, $end), array_merge(['placeholder' => '截止' . $label, 'class' => $classOption + [-1 => 'enddate']], $option['options']));
                    $content = implode('&nbsp;-&nbsp;', $input);
                    break;
                case 'timeRange':
                    $start = 'start_' . $field;
                    $end = 'end_' . $field;
                    !$label && $label = '时间';
                    $input = [];
                    $classOption = (array) ArrayHelper::remove($option['options'], 'class', []);
                    $input[] = Html::input('text', "{$namePrefix}[$start]", ArrayHelper::getValue($searchValue, $start), array_merge(['placeholder' => '开始' . $label, 'class' => $classOption + [-1 => 'starttime']], $option['options']));
                    $input[] = Html::input('text', "{$namePrefix}[$end]", ArrayHelper::getValue($searchValue, $end), array_merge(['placeholder' => '截止' . $label, 'class' => $classOption + [-1 => 'endtime']], $option['options']));
                    $content = implode('&nbsp;-&nbsp;', $input);
                    break;
                case 'select':
                    $htmlMethod = 'dropDownList';
                case 'radio':
                    empty($htmlMethod) && $htmlMethod = 'radioList';
                case 'checkbox':
                    empty($htmlMethod) && $htmlMethod = 'checkboxList';
                    $items = [];
                    // 如果存在别名，则尝试从已经设置的关联表中获取模型
                    $alias = $this->getColumnAlias($column);
                    if ($alias) {
                        if (array_key_exists($alias, $this->withModel)) {
                            $model = $this->withModel[$alias];
                        } else {
                            throw new InvalidParamException("搜索字段设置错误，不存在的关联模型 {$alias}！");
                        }
                    } else {
                        // 否则先设置本表模型
                        $model = $this->model;
                        // 尝试从栏目配置项中，根据别名匹配关联模型
                        foreach ($this->columns as $index => $options) {
                            if (preg_match('/(\w*)\.' . $column . '?$/U', $options['field'], $res)) {
                                if (!empty($res[1])) {
                                    $alias = $res[1];
                                    $model = $this->withModel[$alias];
                                }
                            }
                        }
                    }
                    if (!empty($option['items'])) {
                        // 如果[items]是可回调类型，则从对应方法中获取信息
                        if (is_callable($option['items'])) {
                            $items = call_user_func($option['items'], $label);
                        } elseif (is_array($option['items'])) {
                            $items = $option['items'];
                        } else {
                            throw new InvalidParamException("[items] 配置参数类型错误，必须设置成可回调类型或是键值对数组！");
                        }
                    } else {
                        if (isset($model)) {
                            $callback = $this->getMapMethod($field);
                            if (method_exists(($class = $model::className()), $callback)) {
                                if ($option['type'] === 'select') {
                                    $items = $class::$callback($label);
                                } else {
                                    $items = $class::$callback();
                                }
                            }
                        }
                    }
                    $content = Html::$htmlMethod($name, ArrayHelper::getValue($searchValue, $column), $items, array_merge(['class' => 'search-map'], $option['options']));
                    if ($option['type'] === 'select') {
                        $content = Html::tag('div', $content, ['class' => 'select-box']);
                    } else {
                        $content = Html::tag('div', $label, ['class' => 'search-label']) . $content;
                    }
                    break;
                case 'text':
                    $content = Html::input('text', $name, ArrayHelper::getValue($searchValue, $column), array_merge(['placeholder' => $label], $option['options']));
                    break;
                case 'custom':
                    $content = call_user_func($option['value'], ArrayHelper::getValue($searchValue, $column));
                    break;
            }
            $cells[] = Html::tag('li', $content);
        }
        if ($cells) {
            // 增加搜索按钮
            $cells[] = Hui::successSubmitInput('搜索', ['class' => 'submit-input']) . Html::tag('div', $this->getLoadingImg() . '正在加载...', ['style' => 'display: none;']);
            // 将所有搜索项组装在一起
            $ul = Html::tag('ul', implode('', $cells));
            // 添加表单标签
            return Html::tag('form', $ul, ['method' => 'get', 'class' => 'search-form']);
        } else {
            return '';
        }
    }

    /**
     * Renders the table body.
     * @return string the rendering result.
     */
    protected function renderTableBody()
    {
        $rows = [];
        // 循环数据源输出
        foreach ($this->data as $index => $value) {
            // 获取主键的值
            $key = $this->getKeyValue($value);
            if ($this->beforeRow !== null) {
                $row = call_user_func($this->beforeRow, $value, $key, count($rows));
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($value, $key, count($rows));

            if ($this->afterRow !== null) {
                $row = call_user_func($this->afterRow, $value, $key, count($rows));
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
        }

        if (empty($rows)) {
            $colspan = count($this->columns);

            return "<tbody>\n<tr><td colspan=\"$colspan\" style=\"text-align: center;\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
        } else {
            return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
        }
    }

    /**
     * Renders a table row with the given data model and key.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key associated with the data model
     * @param integer $index the zero-based index of the data model among the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    protected function renderTableRow($value, $key, $rowCount)
    {
        $cells = [];
        foreach ($this->columns as $index => $options) {
            $content = $this->getContent($value, $key, $options);

            if (is_callable($options['options'])) {
                $cellOptions = call_user_func($options['options'], $value, $key);
            } else {
                $cellOptions = $options['options'];
            }
            // 对配置参数[type]的处理
            if (($type = ArrayHelper::getValue($options, 'type', '')) !== '') {
                switch ($type) {
                    case 'checkbox':
                        $content = Html::checkbox('selection[]', false, ['value' => $key]) . $content;
                        $content = Html::tag('label', $content);
                        break;
                    case 'select':
                        $cellOptions['data-action'] = 'selectUpdate';
                    case 'text':
                        empty($cellOptions['data-action']) && $cellOptions['data-action'] = 'textUpdate';

                        $cellOptions['data-field'] = $this->getColumnName($options['field']);
                        $alias = $this->getColumnAlias($options['field']);
                        if (!$alias)  {
                            if ($this->modelClass === null) {
                                throw new InvalidParamException('没有设置默认模型名，不能设置 type => "text" 属性！');
                            } else {
                                $modelClass = $this->modelClass;
                            }
                        } else {
                            $_model = $this->withModel[$alias];
                            $modelClass = $_model::className();
                        }
                        $cellOptions['data-model'] = $modelClass;
                        $targetModel = $this->getColumnValue($value, $options['field'], true);
                        if (!is_object($targetModel)) {
                            $cellOptions['data-key'] = '';
                        } else {
                            $targetKeyValue = $this->getKeyValue($targetModel, $targetModel->primaryKey());
                            $cellOptions['data-key'] = $targetKeyValue;
                        }
                        $cellOptions['href'] = self::createUrl([$this->ajaxUpdateAction]);
                        break;
                    default:
                        // 操作栏的设置
                        if (is_array($type)) {
                            $actionContent = [$content];
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
                                switch ($option['action']) {
                                    case 'edit':
                                        $actionContent[] = Hui::warningBtn('编辑', $href, ['class' => 'edit-fancybox fancybox.iframe']);
                                        break;
                                    case 'view':
                                        $actionContent[] = Hui::primaryBtn('查看', $href, ['class' => 'view-fancybox fancybox.iframe']);
                                        break;
                                    case 'delete':
                                        $href = (array) $href[0];
                                        $actionContent[] = Hui::dangerBtn('删除', $href, ['class' => 'deleteLink', 'data-model' => $this->modelClass, 'data-key' => $key]);
                                        break;
                                    default:
                                        break;
                                }
                            }
                            $content = implode('&nbsp;&nbsp;', $actionContent);
                        }
                        break;
                }
            }
            $style = (array) ArrayHelper::getValue($cellOptions, 'style', []);
            if ($this->isOperationColumn($options)) {
                $defaultStyle['text-align'] = 'center';
                if (count($type) > 0) {
                    $tdWidth = count($type) * 50;
                    $defaultStyle['width'] = "{$tdWidth}px";
                }
                $style = array_merge($defaultStyle, $style);
            }
            if ($styleWidth = ArrayHelper::getValue($options, 'width', '')) {
                $style['width'] = $styleWidth;
            }
            $cellOptions['style'] = $style;
            $cells[] = Html::tag('td', $content, $cellOptions);
        }
        // 每行的options初始化
        if (is_callable($this->rowOptions)) {
            $rowOptions = call_user_func($this->rowOptions, $value, $key);
        } else {
            $rowOptions = $this->rowOptions;
        }
        // 设置主键值到标签属性中
        $rowOptions['data-key'] = is_array($key) ? json_encode($key) : (string) $key;
        $rowOptions['class'] = empty($rowOptions['class']) ? [] : $rowOptions['class'];
        $rowOptions['class'][] = $rowCount % 2 === 0 ? 'odd' : 'even';

        return Html::tag('tr', implode('', $cells), $rowOptions);
    }

    /**
     * Renders the paging.
     * @return string the rendering result
     */
    protected function renderPaging()
    {
        if ($this->paging === false || $this->count <= 0) {
            return '';
        } elseif (\common\traits\FuncTrait::$_pager) {
            return self::linkPager();
        } else {
            return '';
        }
    }

    /**
     * Renders the table header.
     * @return string the rendering result.
     */
    protected function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $options) {
            $cells[] = $this->renderHeaderCell($options);
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);

        return "<thead>\n" . $content . "\n</thead>";
    }

    /**
     * Renders the table footer.
     * @return string the rendering result.
     */
    protected function renderTableFooter()
    {
        $cells = [];
        foreach ($this->columns as $options) {
            $cells[] = $this->renderHeaderCell($options);
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);

        return "<tfoot>\n" . $content . "\n</tfoot>";
    }

    /**
     * Renders the header cell.
     */
    protected function renderHeaderCell($options)
    {
        $content = $options['header'];
        // 获得用户自定义的列属性
        $headerOptions = $this->headerOptions;
        // 如果字段栏目设置中含有items配置项，则存放在列标题中的标签中
        if (ArrayHelper::getValue($options, 'type') === 'select') {
            $itemsOption = ArrayHelper::getValue($options, 'items');
            if (is_callable($itemsOption)) {
                $items = call_user_func($itemsOption, $content);
            } elseif (is_array($itemsOption)) {
                $items = $itemsOption;
            } elseif ($this->modelClass) {
                $alias = $this->getColumnAlias($options['field']);
                $field = $this->getColumnName($options['field']);
                $callback = $this->getMapMethod($field);
                if ($alias) {
                    if (array_key_exists($alias, $this->withModel) && method_exists($this->withModel[$alias], $callback)) {
                        $items = call_user_func([$this->withModel[$alias], $callback], $content);
                    }
                } else {
                    $items = call_user_func([$this->model, $callback], $content);
                }
            }
            
            if (!empty($items)) {
                $headerOptions['data-items'] = json_encode($items);
            }
        }
        // 如果 $sort 开启并且配置项sort不为 false
        if ($this->isSort === true && (ArrayHelper::getValue($options, 'sort') !== false)) {
            $content = $this->renderSortCell($options);
        }
        // 如果字段栏设置中配置项type设置为checkbox，则添加checkbox到列标题中
        if (($type = ArrayHelper::getValue($options, 'type')) === 'checkbox') {
            $content = Html::checkbox('selection_all') . $content;
            $content = Html::tag('label', $content);
        } elseif ($this->isOperationColumn($options)) { // 否则如果不含有字段配置，则表示是操作栏，并设置默认标题
            $content = $content ?: $this->operationTitle;
            $style = (array) ArrayHelper::getValue($headerOptions, 'style', []);
            $defaultStyle['text-align'] = 'center';
            $headerOptions['style'] = array_merge($defaultStyle, $style);
            // 如果含有reset, 则添加列设置到标题中
            if (is_array($type) && in_array('reset', $type)) {
                $action = [$this->action, 'resetTable' => true];
                $this->addParamsTableId($action);
                $content .= '<br>' . Html::a('列设置', $action, ['class' => 'iframe-fancybox fancybox.iframe']);
            }
        }
        // 如果字段栏设置中包含type属性且不为数组，则进行标记
        if (isset($options['type']) && !is_array($options['type'])) {
            $headerClass = (array) ArrayHelper::getValue($options, 'class', []);
            $headerClass[] = 'editable';
            $headerOptions['class'] = $headerClass;
        }

        return Html::tag('th', $content, $headerOptions);
    }

    /**
     * 输出标题栏的排序按钮
     * 
     * @param  array  $options 栏目配置项，主要获取该数组中的field和header键
     * @return string                包含链接的表格标题
     */
    protected function renderSortCell($options)
    {
        $field = $this->getFullField($options['field']);

        !empty($options['header']) ? $label = $options['header'] : $label = '';

        if (!$field) {
            return $label;
        } else {
            return $this->query->sort->link($field, ['label' => $label]);
        }
    }

    /**
     * 当没有数据时渲染的内容
     */
    protected function renderEmpty()
    {
        $tag = ArrayHelper::remove($this->emptyTextOption, 'tag', 'div');

        return Html::tag($tag, $this->emptyText, $this->emptyTextOption);
    }

    /**
     * 设置排序配置
     */
    protected function setSort()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        $sortArr = [];
        // 参数初始化
        $columns = ArrayHelper::resetOptions($this->columns, ['key' => 'field', 'value' => 'header', 'callback' => 'value']);
        foreach ($columns as $option) {
            empty($option['field']) ? $column = '' : $column = $option['field'];
            
            $field = $this->getFullField($column);
            if (!$field) {
                continue;
            }
            $sortArr[] = $field;
        }

        if ($this->query->orderBy) {
            if (current($this->query->orderBy) instanceof \yii\db\Expression) {
                $defaultOrder = current($this->query->orderBy);
            } else {
                // 将 $query->orderBy 中的排序参数形式进行调整
                $defaultOrder = $comma = '';
                foreach ($this->query->orderBy as $sortField => $sortConst) {
                    $sortConst === SORT_ASC ? $sort = 'asc' : $sort = 'desc';
                    $defaultOrder .= $comma . $sortField . ' ' . $sort;
                    $comma = ',';
                }
            }
            $this->query->order($defaultOrder, $sortArr, $this->sortOptions);
        } else {
            $this->query->order($sortArr, $this->sortOptions);
        }
    }

    /**
     * 根据字段名称获取对应的模型
     * 
     * @param  string $column 栏目名称
     * @return object         yii\db\ActiveRecord        
     */
    protected function getRelatedModel($column)
    {
        $alias = $this->getColumnAlias($column);
        if ($alias) {
            $relatedModel = $this->withModel[$alias];    
        } else {
            $relatedModel = $this->model;
        }

        return $relatedModel;
    }

    /**
     * 设置模型的回调函数（包含关联表的回调）
     */
    protected function setModelCallback()
    {
        foreach ($this->columns as $index => $options) {
            // 获取关联模型
            $relatedModel = $this->getRelatedModel($options['field']);
            // 获取当前字段
            $field = $this->getColumnName($options['field']);
            // 设置回调方法名
            $callback = $this->getValueMethod($field);
            // 判断模型中是否存在该方法
            if (method_exists($relatedModel, $callback)) {
                // 进行设置
                $this->modelCallback[$options['field']] = $callback;
            }
        }
    }

    /**
     * 从数据中获取值，将会从字段名中判断是否获取关联表系信息
     *
     * @param object $model    继承自 yii\db\ActiveRecord 的模型
     * @param string $field    当前的字段名称
     * @param bool   $getModel 是否返回模型
     * @return mixed           获取到的值
     */
    protected function getColumnValue($model, $field, $getModel = false)
    {
        $withName = $this->getWithName($field);
        if ($withName !== false) {
            $field = $this->getColumnName($field);
            $withArr = explode('.', $withName);
            $relatedModel = $model;
            foreach ($withArr as $with) {
                $relatedModel = $relatedModel[$with];
            }
            if ($getModel === true) {
                return $relatedModel;
            }
            return $relatedModel[$field];
        } else {
            if ($getModel === true) {
                return $model;
            }
            return $model[$field];
        }
    }

    /**
     * 从每行数据中，获取主键的值
     * 
     * @param  mixed $value       循环中的每行数据
     * @param  array $primaryKeys $model->getPrimary() 的返回结果
     * @return string|array       主键的值
     */
    protected function getKeyValue($value, $primaryKeys = [])
    {
        $key = [];
        !$primaryKeys && $primaryKeys = $this->key;
        array_walk($primaryKeys, function ($keyField) use (&$key, $value) {
            if (isset($value[$keyField])) {
                $key[$keyField] = $value[$keyField];
            } else {
                $key[$keyField] = null;
            }
        });

        return count($key) === 1 ? $key = current($key) : $key;
    }

    /**
     * 根据关联名，实例化所有涉及到的模型，并保存到 $withModel 中
     * 
     * @param string $withName 表关联名
     */
    private function generateWithModel($withName)
    {
        if (empty($this->query->with)) {
            throw new InvalidParamException('当前 $query 中不包含关联表信息，所以只能显示当前表的字段信息！');
        }
        $withNameArr = explode('.', $withName);
        $lastWith = array_pop($withNameArr);
        foreach ($this->query->with as $relation) {
            $rels = explode('.', $relation);
            if (in_array($lastWith, $rels)) {
                foreach ($rels as $index => $rel) {
                    if (!isset($this->withModel[$rel])) {
                        if ($index === 0) {
                            $model = $this->model;
                        } else {
                            $model = $this->withModel[$rels[$index - 1]];
                        }
                        $modelClass = $model->getRelation($rel)->modelClass;
                        $this->withModel[$rel] = new $modelClass;
                    }
                    if ($rel === $lastWith) {
                        break 2;
                    }
                }
            }
        }
    }

    /**
     * 从 $queryWith 中获取到关联名
     * 
     * @param  string $column 栏目名称
     * @return false|string   关联名
     */
    protected function getWithName($column)
    {
        // 获取前导所有别名
        $alias = $this->getColumnAlias($column, true);
        // 和 $queryWith 中的所有配置进行匹配
        $filterRes = array_filter($this->queryWith, function ($with) use ($alias) {
            $pos = strrpos($with, $alias);
            if ($pos !== false) {
                if ($pos > 0) {
                    return $with[$pos - 1] === '.';
                }
                return true;
            } else {
                return false;
            }
        });
        if (!$filterRes) {
            return false;
        } else {
            $aliasPath = current($filterRes);
            $pieces = StringHelper::explode('.', $aliasPath);
            $firstAlias = array_shift($pieces);
            if ($firstAlias == $alias) {
                return $alias;
            } else {
                return $aliasPath;
            }
        }
    }

    /**
     * 尝试根据$data的数据来生成$model和$queryWith
     */
    protected function guessQuery()
    {
        $currentModel = reset($this->data);
        if ($currentModel instanceof \yii\db\ActiveRecord) {
            $this->model = $currentModel;
            $this->query = $currentModel::find();
            $this->generateQueryWith($currentModel);
        } elseif (is_array($currentModel) || $this->data === []) {
            $this->query = new \common\components\Query;
        } else {
            throw new InvalidParamException('$data 的类型有误，第二维的数据既不是 yii\db\ActiveRecord 也不是数组！');
        }
    }

    /**
     * 递归生成$queryWith
     * 
     * @param  object $guessModel yii\db\ActiveRecord
     * @param  string $queryWith  上一级with名称
     * @return string             本级with名称
     */
    private function generateQueryWith($guessModel, $queryWith = '')
    {
        $models = $guessModel->getRelatedRecords();
        $res = '';
        foreach ($models as $with => $model) {
            if (!$model) {
                break;
            }
            $res = $this->generateQueryWith($model, $with);
            if (!$queryWith) {
                $this->queryWith[] = $res;
            }
        }
        $res && $res = '.' . $res;

        return $queryWith . $res;
    }

    /**
     * This function tries to guess the columns to show from the given data
     * if [[columns]] are not explicitly specified.
     */
    protected function guessColumns()
    {
        if ($this->data) {
            $model = reset($this->data);
            if (is_array($model) || is_object($model)) {
                foreach ($model as $column => $value) {
                    $this->columns[] = $column;
                }
            }
        } elseif ($this->query && !empty($this->query->modelClass)) {
            $model = new $this->query->modelClass;
            foreach ($model->attributes as $field => $value) {
                $this->columns[] = $field;
            }
        }
    }

    /**
     * 获取包含别名的完整字段名称，除非是在无法获取到别名
     * 
     * @param  string $column 栏目名称
     * @return string         完整字段名
     */
    protected function getFullField($column)
    {
        $alias = $this->getColumnAlias($column);
        $field = $this->getColumnName($column);

        if (!$alias) {
            if ($this->modelClass !== null) {
                $modelClass = $this->modelClass;
            } elseif (!empty($this->query->modelClass)) {
                $modelClass = $this->query->modelClass;
            } else {
                $modelClass = '';
            }
            if ($modelClass) {
                $alias = lcfirst(StringHelper::basename($modelClass));
            }
        }

        if (!$alias) {
            return $field;
        } elseif (!$field) {
            return '';
        } else {
            return $alias . '.' . $field;
        }
    }

    /**
     * 从字段配置中，获取字段别名
     * 
     * @param  string $column 栏目名称
     * @param  bool   $prefix 前导所有别名
     * @return string         字段别名
     */
    protected function getColumnAlias($column, $prefix = false)
    {
        $columnArr = explode('.', $column);
        array_pop($columnArr);
        if ($columnArr) {
            if ($prefix === true) {
                return implode('.', $columnArr);
            }
            return array_pop($columnArr);
        } else {
            return '';
        }
    }

    /**
     * 从字段配置中，获取字段名称
     * 
     * @param  string $column 栏目名称
     * @return string         字段名称
     */
    protected function getColumnName($column)
    {
        $columnArr = explode('.', $column);

        return array_pop($columnArr);
    }

    /**
     * 判断是否是操作栏
     * 
     * @return boolean
     */
    protected function isOperationColumn($option)
    {
        return isset($option['type']) && is_array($option['type']);
    }

    /**
     * Ajax的成功返回
     */
    public static function success($info = '', $data = null)
    {
        static::ajaxReturn(true, $info, $data);

        exit;
    }

    /**
     * 获取缓存中的信息
     * 
     * @return mixed 缓存的数据
     */
    protected function getCache()
    {
        // 设置保存的键
        $action = $this->action . '-' . $this->tableId;
        // 获取缓存信息
        $cache = cache()->get($this->resetColumnsCacheName, []);
        // 获取当前用户当前动作的配置
        return ArrayHelper::getValue($cache, $action, []);
    }

    /**
     * 设置缓存信息
     * 
     * @param mixed $data 要缓存的数据
     */
    protected function setCache($data)
    {
        // 设置保存的键
        $action = $this->action . '-' . $this->tableId;
        // 获取缓存信息
        $cache = cache()->get($this->resetColumnsCacheName, []);
        // 设置保存的数据
        $cache[$action] = $data;
        // 存入缓存中
        cache($this->resetColumnsCacheName, $cache);
    }

    /**
     * 设置列设置保存的缓存名
     */
    protected function setResetColumnsCacheName()
    {
        // 用户登录情况下，直接使用用户id作为标识，否则使用`$_COOKIE[session]`的值作为标识
        if (u('id')) {
            $uid = u('id');
        } else {
            $uid = ArrayHelper::getValue($_COOKIE, ini_get('session.name'), '');
        }
        $this->resetColumnsCacheName .= $uid;
    }

    /**
     * 当前页面中如果多次使用了本插件，判断当前请求是否该跳过
     */
    protected function isSkipRun()
    {
        $tableId = get($this->tableIdParamName);

        $hasParam = !!$tableId;

        $isCurrentTable = !$tableId && self::$currentTableCount === 1 ?: $tableId == $this->tableId;

        $isResetTable = !!get('resetTable');

        switch (req()->isAjax) {
            case true:
                return $hasParam && !$isCurrentTable;
            case false:
                return $hasParam && !$isCurrentTable && $isResetTable;
        }
    }

    /**
     * 有条件的添加tableId参数到请求链接中
     *
     * @param array $action `self::createUrl`的请求链接
     */
    protected function addParamsTableId(&$action)
    {
        if (self::$currentTableCount > 1) {
            $action[$this->tableIdParamName] = $this->tableId;
        }
    }

    /**
     * 获取单元格最终输出的内容
     * 
     * @param  array|object $value   每行的数据源
     * @param  int          $key     每行的主键
     * @param  array        $options 每行的配置
     * @return string
     */
    protected function getContent($value, $key, $options)
    {
        // 先判断是否具有[value]属性
        if (!empty($options['value'])) {
            if (is_callable($options['value'])) {
                // 列内容的回调定制
                $content = call_user_func($options['value'], $value, $key);
            } else {
                throw new InvalidParamException('配置项 [value] 必须设置成可被回调的类型！');
            }
        } elseif (!empty($options['field'])) {
            // 如果没有[value]属性，则直接获取数据
            $content = $this->getColumnValue($value, $options['field']);
            // 判断是否存在模型的回调格式化方法
            if (isset($this->modelCallback[$options['field']])) {
                $relatedModel = $this->getRelatedModel($options['field']);
                $field = $this->getColumnName($options['field']);
                $content = $relatedModel->{$this->modelCallback[$options['field']]}($content, $key);
            }
        } else {
            $content = '';
        }

        return $content;
    }

    /**
     * 从对应模型中获取字段的描述
     *
     * @param  string $field 完整字段名
     * @return string        字段描述
     */
    protected function getFieldLabel($fullField)
    {
        // 获取关联模型
        $relatedModel = $this->getRelatedModel($fullField);
        // 从当前模型中获取属性标签
        $labels = $relatedModel->attributeLabels();
        // 获取字段名
        $field = $this->getColumnName($fullField);
        $labelField = strpos($field, '_') === 0 ? substr($field, 1) : $field;

        return isset($labels[$labelField]) ? $labels[$labelField] : '';
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
     * 获取loading图片标签
     * 
     * @return string loading图片标签
     */
    protected function getLoadingImg()
    {
        if ($this->loadingImgSrc === null) {
            $src = 'data:image/gif;base64,R0lGODlhEAAQAPQAAP///5mZmfz8/NTU1OTk5MrKytDQ0PX19evr683NzeHh4d7e3vj4+Ojo6PLy8tfX19vb2wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH+FU1hZGUgYnkgQWpheExvYWQuaW5mbwAh+QQACgAAACH/C05FVFNDQVBFMi4wAwEAAAAsAAAAABAAEAAABVAgII5kaZ6lMBRsISqEYKqtmBTGkRo1gPAG2YiAW40EPAJphVCREIUBiYWijqwpLIBJWviiJGLwukiSkDiEqDUmHXiJNWsgPBMU8nkdxe+PQgAh+QQACgABACwAAAAAEAAQAAAFaCAgikfSjGgqGsXgqKhAJEV9wMDB1sUCCIyUgGVoFBIMwcAgQBEKTMCA8GNRR4MCQrTltlA1mCA8qjVVZFG2K+givqNnlDCoFq6ioY9BaxDPI0EACzxQNzAHPAkEgDAOWQY4Kg0JhyMhACH5BAAKAAIALAAAAAAQABAAAAVgICCOI/OQKNoUSCoKxFAUCS2khzHvM4EKOkPLMUu0SISC4QZILpgk2bF5AAgQvtHMBdhqCy6BV0RA3A5ZAKIwSAkWhSwwjkLUCo5rEErm7QxVPzV3AwR8JGsNXCkPDIshACH5BAAKAAMALAAAAAAQABAAAAVSICCOZGmegCCUAjEUxUCog0MeBqwXxmuLgpwBIULkYD8AgbcCvpAjRYI4ekJRWIBju22idgsSIqEg6cKjYIFghg1VRqYZctwZDqVw6ynzZv+AIQAh+QQACgAEACwAAAAAEAAQAAAFYCAgjmRpnqhADEUxEMLJGG1dGMe5GEiM0IbYKAcQigQ0AiDnKCwYpkYhYUgAWFOYCIFtNaS1AWJESLQGAKq5YWIsCo4lgHAzFmPEI7An+A3sIgc0NjdQJipYL4AojI0kIQAh+QQACgAFACwAAAAAEAAQAAAFXyAgjmRpnqhIFMVACKZANADCssZBIkmRCLCaoWAIPm6FBUkwJIgYjR5LN7INSCwHwYktdIMqgoNFGhQQpMMt0WCoiGDAAvkQMYkIGLCXQI8OQzdoCC8xBGYFXCmLjCYhADsAAAAAAAAAAAA=';
        } else {
            $src = $this->loadingImgSrc;
        }

        return Html::img($src, ['alt' => 'Loading Image']);
    }
}
