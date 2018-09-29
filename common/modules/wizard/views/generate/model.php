<?php
/**
 * This is the template for generating the model class of a specified table.
 */

echo "<?php\n";
?>

namespace <?= $namespace ?>;

use Yii;

/**
 * 这是表 `<?= $tableName ?>` 的模型
 */
class <?= $className ?> extends <?= '\\' . $generator->baseClass . "\n" ?>
{
<?php if ($generator->aliasFlag === true): ?>
    public static function tableName()
    {
        return '{{%<?= $generator->tableName ?>}}';
    }

<?php endif ?>
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . "\n        " ?>];
    }

    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach ?>
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
<?php if (isset($compares['equal'])): ?>
            ->filterWhere([
    <?php foreach ($compares['equal'] as $column): ?>
            <?= "'$alias.$column' => \$this->$column,\n" ?>
    <?php endforeach ?>
        ])
<?php endif ?>
<?php if (isset($compares['like'])):
        foreach ($compares['like'] as $column): ?>
            <?= "->andFilterWhere(['like', '$alias.$column', \$this->$column])\n" ?>
<?php   endforeach ?>
<?php endif ?>
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/
}
