<?php

namespace common\components;

/**
 * Redis的基类，增强一些命令的作用
 *
 * @author ChisWill
 */
class Redis extends \yii\redis\Connection
{
    public function __call($name, $params)
    {
        if (!in_array($name, $this->redisCommands)) {
            $this->redisCommands[] = strtoupper($name);
        }
        try {
            list($command, $params) = $this->beforeCommand($name, $params);

            return $this->afterCommand($name, parent::__call($command, $params));
        } catch (\yii\db\Exception $e) {
            if (strpos($e->getMessage(), 'unknown command \'' . $name . '\'') !== false) {
                array_pop($this->redisCommands);

                return parent::__call($name, $params);
            }
        }
    }

    protected function beforeCommand($name, $params)
    {
        switch ($name) {
            case 'set':
                if (count($params) === 3) {
                    $name = 'setex';
                    $params = [$params[0], $params[2], $params[1]];
                }
                break;
            case 'sets':
                $name = 'set';
                $params[1] = serialize($params[1]);
                return $this->beforeCommand($name, $params);
            case 'gets':
                $name = 'get';
                break;
        }

        return [$name, $params];
    }

    protected function afterCommand($name, $value)
    {
        switch ($name) {
            case 'gets':
                return $value === null ? false : unserialize($value);
            default:
                return $value === null ? false : $value ;
        }
    }
}
