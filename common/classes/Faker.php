<?php

namespace common\classes;

use Yii;
use common\helpers\ArrayHelper;

class Faker extends \yii\base\Object
{
    private $_faker;
    private $_zh;
    private $_surname;

    public function init()
    {
        require Yii::getAlias('@vendor/fzaninotto/faker/src/autoload.php');

        $this->_faker = \Faker\Factory::create();
        $this->_zh = require Yii::getAlias('@common/classes/params/zh-lang.php');
        $this->_surname = require Yii::getAlias('@common/classes/params/zh-surname.php');
    }

    // 当调用当前类未定义的属性时，将先调用对应的getter，如果不存在则调用Faker自身的属性
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } else {
            return $this->_faker->$name;
        }
    }

    // 生成中文名
    public function getName()
    {
        $surname = ArrayHelper::random($this->_surname);
        $name = $this->percent(30) ? ArrayHelper::random($this->_zh) : ArrayHelper::random($this->_zh, 2);

        return $surname . $name;
    }

    // 生成英文名
    public function getEname()
    {
        return $this->_faker->name;
    }

    // 生成邮件
    public function getEmail()
    {
        return $this->_faker->email;
    }

    // 生成手机号
    public function getMobile()
    {
        $prefix = [3, 5, 8];
        return 1 . ArrayHelper::random($prefix) . $this->number(9);
    }

    // 生成图片
    public function getImage()
    {
        return $this->_faker->imageUrl();
    }

    // 根据设定的概率，返回布尔值
    public function percent($percent = 50)
    {
        return mt_rand(1, 10000) <= $percent * 100;
    }

    // 随机生成一个汉字
    public function getChar()
    {
        return $this->char();
    }

    /**
     * 生成随机汉字.
     * 设定一个参数时，表示生成指定长度汉字
     * 设定两个参数时，表示生成区间长度的汉字
     * 
     * @return string
     */
    public function char()
    {
        switch (func_num_args()) {
            case 1:
                $length = func_get_arg(0);
                break;
            case 2:
                $min = func_get_arg(0);
                $max = func_get_arg(1);
                $length = mt_rand($min, $max);
                break;
            default:
                $length = 1;
                break;
        }
        return ArrayHelper::random($this->_zh, $length);
    }

    /**
     * 生成指定长度的随机数字.
     * 不设定参数时，表示生成一个小于100的随机数
     * 设定一个参数时，表示生成固定长度数字
     * 设定两个参数时，表示生成指定区间长度的数字
     * 
     * @return int
     */
    public function number()
    {
        switch (func_num_args()) {
            case 1:
                $base = pow(10, func_get_arg(0) - 1);
                return mt_rand($base, $base * 10 - 1);
            case 2:
                $min = pow(10, func_get_arg(0) - 1);
                $max = pow(10, func_get_arg(1) - 1);
                return mt_rand($min, $max * 10 - 1);
            default:
                return mt_rand(1, 100);
        }
    }
}
