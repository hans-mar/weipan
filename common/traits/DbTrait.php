<?php

namespace common\traits;

use Yii;

/**
 * DB类的助手方法
 *
 * @author ChisWill
 */
trait dbTrait 
{
    /**
     * 直接执行sql语句的方法
     * 
     * @param  string $sql sql语句
     * @return object      yii\db\Command
     */
    public static function db($sql = null)
    {
        return Yii::$app->db->createCommand($sql);
    }

    /**
     * 去除表前缀的引用符
     * 
     * @param  string $tableName 表名
     * @return string            去除引用符后的表名
     */
    private static function _trimTableQuote($tableName)
    {
        return str_replace(['{{%', '}}'], '', $tableName);
    }

    /**
     * 快捷插入表数据的方法
     * Note:如果 $rows 为空，则column必须是键值对的数组
     * 
     * @param  string $table   表名
     * @param  array  $columns 字段名
     * @param  array  $rows    每行更新的值
     * @return int             成功插入的行数
     */
    public static function dbInsert($table, $columns, $rows = [])
    {
        $table = self::_trimTableQuote($table);
        
        if (empty($rows)) {
            return Yii::$app->db->createCommand()->insert('{{%' . $table . '}}', $columns)->execute();
        } else {
            return Yii::$app->db->createCommand()->batchInsert('{{%' . $table . '}}', $columns, $rows)->execute();
        }
    }

    /**
     * 快捷更新表数据的方法
     * 
     * @param  string $table     表名
     * @param  array  $columns   字段名
     * @param  string $condition 条件
     * @param  array  $params    条件中需要绑定的参数
     * @return int               成功更新的行数
     */
    public static function dbUpdate($table, $columns, $condition = '', $params = [])
    {
        $table = self::_trimTableQuote($table);
        
        return Yii::$app->db->createCommand()->update('{{%' . $table . '}}', $columns, $condition, $params)->execute();
    }

    /**
     * 快捷删除表数据的方法
     * 
     * @param  string $table     表名
     * @param  string $condition 条件
     * @param  array  $params    条件中需要绑定的参数
     * @return int               成功删除的行数
     */
    public static function dbDelete($table, $condition = '', $params = [])
    {
        $table = self::_trimTableQuote($table);
        
        return Yii::$app->db->createCommand()->delete('{{%' . $table . '}}', $condition, $params)->execute();
    }

    /**
     * 启用事务，有两种模式
     * -回调模式（传入回调方法，传出回调方法的返回值）：执行事务处理的代码都在回调方法中执行，可以起到各事务相互隔离的作用
     * -全局模式（传入事务隔离级别，传出事务对象）：开启后，之后的所有数据库操作都将被一个事务进行处理
     *
     * @param  callable|string|null     $callback       执行事务处理的PHP回调函数
     * @param  string|null              $isolationLevel 事物隔离级别
     * @return mixed|yii\db\Transaction
     */
    public static function dbTransaction($callback = null, $isolationLevel = null)
    {
        if (is_callable($callback)) {
            return Yii::$app->db->transaction($callback, $isolationLevel);
        } else {
            return Yii::$app->db->beginTransaction($callback);
        }
    }

    /**
     * 快捷创建一个查询构建器
     * 
     * @return object common\components\Query
     */
    public static function dbQuery()
    {
        return new \common\components\Query;
    }

    /**
     * 快捷创建一个表达式
     * 
     * @param  string $expression 表达式语句
     * @param  array  $params     语句中需要绑定的参数
     * @return object             yii\db\Expression
     */
    public static function dbExpression($expression, $params = [])
    {
        return new \yii\db\Expression($expression, $params);
    }

    /**
     * 快捷创建一个DB缓存依赖对象
     *
     * @param  string $sql 缓存条件依赖的sql语句
     * @return object      yii\caching\DbDependency
     */
    public static function dbDependency($sql)
    {
        return new \yii\caching\DbDependency(['sql' => $sql]);
    }
}
