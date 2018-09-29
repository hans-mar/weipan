<?php

namespace common\modules\wizard\generators\model;

use Yii;
use yii\db\Schema;
use yii\helpers\Inflector;

/**
 * This generator will generate one ActiveRecord classes for the specified database table.
 *
 * @author ChisWill
 */
class Generator extends \common\modules\wizard\Generator
{
    // 公共属性
    public $alias;
    public $aliasFlag = false;
    // 虚拟字段
    public $tableName;
    public $modelNamespace;
    public $isExtend;
    // 默认配置设定
    public $mainNamespace = 'common\models';
    public $baseClass = 'common\components\ARModel';
    public $generateLabelsFromComments = true;

    public function rules()
    {
        return [
            [['tableName'], 'required', 'message' => '{attribute} 不能为空~！'],
            [['tableName', 'modelNamespace'], 'filter', 'filter' => 'trim'],
            [['tableName'], 'validateTableName'],
            [['isExtend'], 'safe'],
            [['modelNamespace'], 'validateModelNamespace', 'skipOnEmpty' => false]
        ];
    }

    public function attributeLabels()
    {
        return [
            'tableName' => '表名',
            'isExtend' => '继承选项'
        ];
    }

    /**
     * Validates the [[tableName]] attribute.
     */
    public function validateTableName()
    {
        $db = $this->getDbConnection();
        if (strpos($this->tableName, '->') !== false) {
            $pieces = explode('->', $this->tableName);
            $this->tableName = $pieces[0];
            $this->alias = $pieces[1];
            $this->aliasFlag = true;
        } else {
            $this->alias = $this->tableName;
        }
        $tableName = $db->tablePrefix . $this->tableName;
        $class = $this->generateClassName($tableName);

        if ($this->isReservedKeyword($class)) {
            $this->addError('tableName', "Table '$tableName' will generate a class which is a reserved PHP keyword.");
        } elseif ($db->getTableSchema($tableName, true) === null) {
            $this->addError('tableName', "Table '{$tableName}' does not exist.");
        }
    }

    /**
     * Validates the [[modelNamespace]] attribute.
     */
    public function validateModelNamespace()
    {
        if (!$this->modelNamespace && !$this->isExtend) {
            $this->addError('modelNamespace', '继承选项与命名空间必须选择填一个~！');
        } elseif ($this->modelNamespace) {
            $this->modelNamespace = trim(str_replace('/', '\\', $this->modelNamespace), '\\');
            try {
                Yii::getAlias('@' . explode('\\', $this->modelNamespace)[0]);
            } catch (\yii\base\InvalidParamException $e) {
                $this->addError('modelNamespace', $e->getMessage());
            }
        }
    }

    /**
     * Generates the default condition for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated condition
     */
    public function generateCompares($table)
    {
        $compares = [];
        foreach ($table->columns as $column) {
            switch ($column->type) {
                // 数值型都采用“精确匹配”模式
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $compares['equal'][] = $column->name;
                    break;
                // 非数值型都采用“模糊匹配”模式
                default: 
                    $compares['like'][] = $column->name;
            }
        }
        return $compares;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    public function generateLabels($table)
    {
        $labels = [];
        foreach ($table->columns as $column) {
            if ($this->generateLabelsFromComments && !empty($column->comment)) {
                $labels[$column->name] = $column->comment;
            } elseif (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name);
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

    /**
     * Generates validation rules for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            }
            switch ($column->type) {
                case Schema::TYPE_TEXT:
                    $types['default'][] = $column->name;
                    break;
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $types['safe'][] = $column->name;
                    break;
                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
                    // 如果字段中包含email,则给予email验证规则
                    if (preg_match('/\w?email$/Ui', $column->name)) {
                        $types['email'][] = $column->name;
                    }
            }
        }
        $rules = [];
        foreach ($types as $type => $columns) {
            if ($type === 'default') {
                $rules[] = "[['" . implode("', '", $columns) . "'], 'default', 'value' => '']";
            } else {
                $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
            }
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], 'string', 'max' => $length]";
        }

        // Unique indexes rules
        try {
            $db = $this->getDbConnection();
            $uniqueIndexes = $db->getSchema()->findUniqueIndexes($table);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount == 1) {
                        $rules[] = "[['" . $uniqueColumns[0] . "'], 'unique']";
                    } elseif ($attributesCount > 1) {
                        $labels = array_intersect_key($this->generateLabels($table), array_flip($uniqueColumns));
                        $lastLabel = array_pop($labels);
                        $columnsList = implode("', '", $uniqueColumns);
                        $rules[] = "[['" . $columnsList . "'], 'unique', 'targetAttribute' => ['" . $columnsList . "'], 'message' => 'The combination of " . implode(', ', $labels) . " and " . $lastLabel . " has already been taken.']";
                    }
                }
            }
        } catch (\yii\base\NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        return $rules;
    }

    /**
     * Generates a class name from the specified table name.
     * @param string $tableName the table name (which may contain schema prefix)
     * @return string the generated class name
     */
    public function generateClassName($tableName)
    {
        $db = $this->getDbConnection();
        $patterns = [];
        $patterns[] = "/^{$db->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$db->tablePrefix}$/";
        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^' . str_replace('*', '(\w+)', $pattern) . '$/';
        }
        $className = $tableName;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                break;
            }
        }

        return Inflector::id2camel($className, '_');
    }

    /**
     * Checks if any of the specified columns is auto incremental.
     * @param \yii\db\TableSchema $table the table schema
     * @param array $columns columns to check for autoIncrement property
     * @return boolean whether any of the specified columns is auto incremental.
     */
    private function isColumnAutoIncremental($table, $columns)
    {
        foreach ($columns as $column) {
            if (isset($table->columns[$column]) && $table->columns[$column]->autoIncrement) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Connection the DB connection as specified by [[db]].
     */
    public function getDbConnection()
    {
        return Yii::$app->get('db', false);
    }
}
