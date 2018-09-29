<?php

namespace common\helpers;

class Html extends \yii\helpers\BaseHtml
{
    /**
     * @see \yii\helpers\BaseHtml::a()
     */
    public static function a($text = '', $url = null, $options = [])
    {
        if ($url !== null) {
            if (is_string($url)) {
                $url = (array) $url;
            }
            $url[0] = Inflector::camel2id($url[0]);
            $options['href'] = Url::to($url);
        }
        return static::tag('a', $text, $options);
    }

    /**
     * 生成 span 标签
     * 
     * @param  string $content span标签的文本
     * @param  array  $options html属性
     * @return string          span标签的html代码
     */
    public static function span($content = '', $options = [])
    {
        return static::tag('span', $content, $options);
    }

    /**
     * 快捷静态调用方法，诸如使用 Html::successSpan() 的方式，将会自动获取带颜色的 Html 标签
     */
    public static function __callStatic($name, $args)
    {
        $pieces = explode('-', Inflector::camel2id($name, '-'));
        $tag = array_pop($pieces);
        $color = array_shift($pieces);
        switch ($color) {
            case 'success':
                $colorCode = '#36F';
                break;
            case 'warning':
                $colorCode = '#F91';
                break;
            case 'error':
                $colorCode = '#F11';
                break;
            case 'finish':
                $colorCode = '#3B3';
                break;
            case 'red':
                $colorCode = '#E31';
                break;
            case 'green':
                $colorCode = '#5C5';
                break;
            default:
                $colorCode = '#E3E';
                break;
        }
        if (empty($args[1]['style'])) {
            $style = [];
        } else {
            $style = (array) rtrim($args[1]['style'], ';');
        }
        $style[] = "color:{$colorCode};";
        $args[1]['style'] = implode(';', $style);

        return static::tag($tag, $args[0], $args[1]);
    }
}
