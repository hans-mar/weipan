<?php

namespace common\helpers;

/**
 * H-ui 前端框架的快捷使用的助手类
 * 
 * @author ChisWill
 */
class Hui
{
    /**
     * @see common\helpers\Html::a()
     */
    public static function btn($text = '', $url = null, $options = [])
    {
        $options = self::addBtnDefault($options);

        return Html::a($text, $url, $options);
    }

    /**
     * @see yii\helpers\BaseHtml::input()
     */
    public static function input($type, $name = null, $value = null, $options = [])
    {
        $options = self::addBtnDefault($options);

        return Html::input($type, $name, $value, $options);
    }

    /**
     * @see yii\helpers\BaseHtml::submitInput()
     */
    public static function submitInput($label = '提交', $options = [])
    {
        $options = self::addBtnDefault($options, 'size-M');

        return Html::submitInput($label, $options);
    }

    /**
     * H-ui 专属快捷静态调用，将会自动生成对应class的元素
     */
    public static function __callStatic($name, $args)
    {
        $pieces = explode('-', Inflector::camel2id($name, '-'));
        $tag = array_pop($pieces);
        $color = array_shift($pieces);
        switch ($tag) {
            case 'btn':
                $url = ArrayHelper::getValue($args, 1);
                $options = ArrayHelper::getValue($args, 2, []);
                return Hui::btn($args[0], $url, self::addBtnColor($options, $color));
            case 'input':
                $type = array_pop($pieces);
                if ($type === 'submit') {
                    $options = ArrayHelper::getValue($args, 1, []);
                    $options['value'] = $args[0];
                    return Hui::submitInput($args[0], self::addBtnColor($options, $color, ''));
                } elseif (!$type) {
                    $type = $color;
                    switch ($type) {
                        case 'text':
                            $options = ArrayHelper::getValue($args, 2, []);
                            $options = self::addDefaultClass($options, 'input-text');
                            return Html::textInput($args[0], $args[1], $options);
                    }
                }
            default: // 默认执行函数签名形如 function ($text, $options)
                $options = ArrayHelper::getValue($args, 1, []);
                if ($color) {
                    $options = self::addTextColor($options, $color);
                }
                return Html::tag($tag, $args[0], $options);
        }
    }

    /**
     * 增加文字类型的颜色样式，可选的颜色前缀有
     * -`primary`
     * -`secondary`
     * -`success`
     * -`error`
     * -`warning`
     * -其他标准色:`red`、`black`、`green`、`blue`、`white`、`orange`
     */
    private static function addTextColor($options, $color)
    {
        $options['class'] = (array) ArrayHelper::getValue($options, 'class', []);
        $options['class'][] = $color == 'disabled' ? $color : 'c-' . $color;

        return $options;
    }

    /**
     * 增加按钮类型的颜色样式，可选的颜色前缀有
     * -`primary`
     * -`secondary`
     * -`success`
     * -`danger`
     * -`warning`
     * -`disabled`
     */
    private static function addBtnColor($options, $color, $suffix = '-outline')
    {
        $options['class'] = (array) ArrayHelper::getValue($options, 'class', []);
        $options['class'][] = $color == 'disabled' ? $color : 'btn-' . $color . $suffix;

        return $options;
    }

    /**
     * 默认增加按钮圆角、大小等基础样式
     */
    private static function addBtnDefault($options, $defaultClass = '')
    {
        $options['class'] = (array) ArrayHelper::getValue($options, 'class', []);
        if ($defaultClass) {
            $options['class'][] = $defaultClass;
        }
        $options['class'][] = 'btn radius';
        foreach ($options['class'] as $item) {
            if (strpos($item, 'size-') !== false) {
                $sizeFlag = true;
            }
        }
        if (empty($sizeFlag)) {
            $options['class'][] = ' size-S';
        }

        return $options;
    }

    /**
     * 在现有配置中增加默认class属性
     */
    private static function addDefaultClass($options, $addClass = '')
    {
        $options['class'] = (array) ArrayHelper::getValue($options, 'class', []);
        $options['class'][] = $addClass;

        return $options;
    }
}
