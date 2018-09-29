<?php

namespace common\traits;

use Yii;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;

/**
 * ARQuery和Query的共通方法
 *
 * @author ChisWill
 */
trait QueryTrait
{
    use \common\traits\ChisWill;

    /**
     * @var yii\data\Sort
     */
    protected $_sort = null;

    /**
     * 使用 yii\db\activeQuery 的方式进行数据更新
     * ```php
     * User::find()->with('comments')->where('uid = 3')->update(['comments.state' => 1]);
     * ```
     * @param  array   $columns 更新的字段
     * @return integer          成功更新数量
     */
    public function update($columns)
    {
        $sql = $this->joinWith($this->with)->rawSql;
        if (!$this->where) {
            $sqlPieces = StringHelper::explode(' LEFT JOIN ', $sql);
            $fromPieces = StringHelper::explode(' FROM ', array_shift($sqlPieces));
            $from[] = $fromPieces[1];
            $joins = $sqlPieces;
        } else {
            preg_match('/FROM (.*)(\s*LEFT JOIN (.*)\s*)?WHERE/U', $sql, $res);
            $from = [$res[1]];
            $joinPart = isset($res[3]) ? $res[3] : '';
            $joins = StringHelper::explode(' LEFT JOIN ', $joinPart);
        }

        $on = [];
        foreach ($joins as $join) {
            $pieces = explode(' ON ', $join);
            $from[] = $pieces[0];
            $on[] = $pieces[1];
        }

        $buildCondition = function ($where) use (&$buildCondition) {
            if (is_array($where)) {
                if (isset($where[1]) && is_array($where[1])) {
                    $where[1] = $buildCondition($where[1]);
                }
                return "({$where[1]}) {$where[0]} ({$where[2]})";
            } else {
                return $where;
            }
        };

        if ($on) {
            if ($this->where) {
                $where = ['and', $this->where, implode(' and ', $on)];
            } else {
                $where = $on[0];
            }
        } else {
            $where = $this->where;
        }
        $where = $buildCondition($where);

        $column = $d = '';
        foreach ($columns as $field => $value) {
            $column .= $d . '`' . $field . '`="' . $value . '"';
            $d = ',';
        }
        $updateSql = 'UPDATE ' . implode(',', $from) . ' SET ' . $column;
        if ($where) {
            $updateSql .= ' WHERE ' . $where;
        }

        return self::db($updateSql)->execute();
    }

    /**
     * 快速获取 ActiveDataProvider 对象
     * 
     * @param  integer|array  $pageOptions 分页参数，如果为数字时，则表示每页个数
     * @param  string|array   $sortOptions 排序参数，如果为字符串时，则表示默认排序
     * @return object
     */
    public function getData($pageOptions = [], $sortOptions = [])
    {
        if (is_integer($pageOptions)) {
            $pagination = ['pageSize' => $pageOptions];
        } else {
            $pagination = $pageOptions;
        }
        if (is_string($sortOptions)) {
            $sort = ['defaultOrder' => $this->_convertSortParams($sortOptions)];
        } else {
            $sort = $sortOptions;
        }
        return new \yii\data\ActiveDataProvider([
            'query' => $this,
            'pagination' => $pagination,
            'sort' => $sort
            ]);
    }

    /**
     * 类似 common\helpers\ArrayHelper 的map方法，可以直接从表中搜出键值对的数据
     * 
     * @param  string $key   当作key的字段
     * @param  string $value 当作值的字段，如果不填表示搜出全部字段
     * @return array         键值对的表数据
     */
    public function map($key, $value = null)
    {
        if ($value === null) {
            $select = '*';
            $findMethod = 'all';
        } else {
            $select = [$value];
            $findMethod = 'column';
        }

        return $this->select($select)->indexBy($key)->$findMethod();
    }

    /**
     * @see common\traits\FuncTrait::paginate()
     */
    public function paginate($pageSize = PAGE_SIZE)
    {
        return \common\traits\FuncTrait::paginate($this, $pageSize);
    }

    /**
     * 当调用 paginate() 后，调用本方法可获取数据总数
     * 
     * @return integer 数据总数 
     */
    public function getTotalCount()
    {
        return \common\traits\FuncTrait::$_totalCount;
    }

    /**
     * @return string 获取当前执行的sql语句
     */
    public function getRawSql()
    {
        return $this->createCommand()->getRawSql();
    }

    /**
     * 增加一个类型识别，兼容 yii\data\Sort
     */
    public function orderBy($sort)
    {
        if ($sort instanceof \yii\data\sort) {
            return parent::orderBy($sort->orders);
        } else {
            return parent::orderBy($sort);
        }
    }

    /**
     * 转换排序参数，从字符串形式转为数组形式，主要为了适配 \yii\data\Sort 的 defaultOrder 参数形式
     * ```php
     * $sort = 'name desc, id, email asc';
     * $res = $this->_convertSortParams($sort);
     * print_r($res);
     * 
     * [
     *     'name' => SORT_DESC,
     *     'id' => SORT_ASC,
     *     'email' => SORT_ASC
     * ];
     * 
     * ```
     * 
     * @param  string $sort 排序条件
     * @return array        yii2的标准排序参数
     */
    protected function _convertSortParams($sort)
    {
        $sortArr = StringHelper::explode(',', $sort);
        $result = array_map(function ($item) {
            $pieces = preg_split('/\s+/', $item);
            if (isset($pieces[1]) && strtolower($pieces[1]) === 'desc') {
                $order = SORT_DESC;
            } else {
                $order = SORT_ASC;
            }
            $pieces[1] = $order;
            return $pieces;
        }, $sortArr);
        
        return ArrayHelper::map($result, 0, 1);
    }

    /**
     * @param string|array|Expression $defaultOrder   可以是字符串形式的 order by 的子句或 yii\db\Expression，也可以是 $sortAttributes
     * @param array                   $sortAttributes
     * @param array                   $sortOptions    yii\data\Sort 的配置参数 
     *
     * 当 $defaultOrder 为数组时，$sortAttributes 则表示 $sortOptions
     * $sortAttributes 的形式如下：
     * 1.['id', 'name']
     * 2.['id' => '序号', 'name' => '名字']
     * 3.['id' => '序号', 
     *    'name' => [
     *       'asc' => ['name' => SORT_ASC],
     *       'desc' => ['name' => SORT_DESC],
     *       'default' => SORT_DESC,
     *       'label' => '名字'
     *     ]
     *   ]
     * 第3种形式，也是 yii\data\Sort 默认参数形式，详情参看yii2排序章节
     *
     * @return yii\data\Sort
     */
    public function setSort($defaultOrder = '', $sortAttributes = [], $sortOptions = [])
    {
        // 根据参数类型，调整统一化
        if (is_array($defaultOrder)) {
            $sortOptions = $sortAttributes;
            $sortAttributes = $defaultOrder;
            $defaultOrder = '';
        }
        // 获取对应模型的标签
        if (!empty($this->modelClass)) {
            $labels = (new $this->modelClass)->attributeLabels();
        } else {
            $labels = [];
        }
        $attributes = [];
        // 循环整合 $sortAttributes
        foreach ($sortAttributes as $key => $value) {
            if (is_numeric($key)) {
                $field = $value;
                $label = ArrayHelper::getValue($labels, $field, '');
            } elseif (is_string($value)) {
                $field = $key;
                $label = $value;
            }

            if (is_array($value)) {
                $_attributes = $value;
            } else {
                $_attributes = [
                    $field => [
                        'default' => SORT_DESC,
                        'label' => $label
                    ]
                ];
            }
            $attributes = array_merge($attributes, $_attributes);
        }
        
        // 如果 $defaultOrder 为字符串，则调整成数组
        if (is_string($defaultOrder)) {
            $orderPieces = StringHelper::explode(',', $defaultOrder);
            $defaultOrder = $this->_convertSortParams($defaultOrder);
            // 循环整合 $defaultOrder, 并再次调整 $attributes
            foreach ($orderPieces as $key => $field) {
                $pieces = preg_split('/\s+/', $field);
                $field = $pieces[0];
                if (!array_key_exists($field, $attributes)) {
                    $newAttribute = [
                        $field => [
                            'default' => SORT_DESC,
                            'label' => ArrayHelper::getValue($labels, $field, '')
                        ]
                    ];
                    $attributes = array_merge($attributes, $newAttribute);
                }
            }
        }

        $this->_sort = new \yii\data\Sort([
            'defaultOrder' => $defaultOrder,
            'attributes' => $attributes
        ]);

        foreach ($sortOptions as $name => $value) {
            $this->_sort->$name = $value;
        }

        return $this;
    }

    /**
     * 获取当前设置的 yii\data\Sort, 如果未设置则返回null
     */
    public function getSort()
    {
        return $this->_sort;
    }

    /**
     * 该方法主要添加了适应 common\Widgets\Table 的自动排序功能，将会自动获取URL参数中的排序参数
     * @see self::setSort()
     */
    public function order($defaultOrder = '', $sortAttributes = [], $sortOptions = [])
    {
        // 根据参数类型，调整统一化
        if (is_array($defaultOrder)) {
            $sortOptions = $sortAttributes;
            $sortAttributes = $defaultOrder;
            $defaultOrder = '';
        }
        
        if (!$sortAttributes) {
            $sortParam = ArrayHelper::getValue($sortOptions, 'sortParam', 'sort');
            if (($sort = get($sortParam))) {
                $sort = ltrim($sort, '-');
                $sortAttributes[] = $sort;
            }
        }

        $this->setSort($defaultOrder, $sortAttributes, $sortOptions);
        // 当 orderBy 为 yii\db\Expression 时，无法通过 yii\data\Sort 中获取到默认排序
        if ($this->orderBy && !$this->getSort()->orders) {
            $orders = $this->orderBy;
        } else {
            $orders = $this->getSort();
        }

        return $this->orderBy($orders);
    }

    /**
     * common\widgets\Linkage 组件的快捷调用
     * 
     * @param  array $columns 列的配置参数
     * @param  array $options 其他杂项配置参数
     * @return string         表格的HTML内容         
     */
    public function getLinkage($columns = [], $params = [])
    {
        $params = array_merge($params, [
            'query' => $this,
            'columns' => $columns
        ]);

        return \common\widgets\Linkage::widget($params);
    }

    /**
     * common\widgets\Table 组件的快捷调用
     * 
     * @param  array $columns 列的配置参数
     * @param  array $options 其他杂项配置参数
     * @return string         表格的HTML内容         
     */
    public function getTable($columns = [], $params = [])
    {
        $params = array_merge($params, [
            'query' => $this,
            'columns' => $columns
        ]);
        return \common\widgets\Table::widget($params);
    }

    /**
     * common\widgets\Tree 组件的快捷调用
     * 
     * @param  array  $params 配置项参数
     * @return object         common\widgets\Tree 对象
     */
    public function getTree($params = [])
    {
        $params = array_merge($params, [
            'class' => 'common\widgets\Tree',
            'query' => $this
        ]);
        return Yii::createObject($params);
    }
}
