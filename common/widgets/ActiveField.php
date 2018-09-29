<?php

namespace common\widgets;

use Yii;
use common\helpers\Html;
use common\helpers\Inflector;
use common\helpers\ArrayHelper;
/**
 * 加强 ActiveField 的功能，增加公共的快捷方法
 * 
 * @author ChisWill
 */
class ActiveField extends \yii\widgets\ActiveField
{
    use \common\traits\FuncTrait;

    public $showLabel = false;
    public $template = "{input}\n{hint}\n{error}";

    public function init()
    {
        parent::init();

        if ($this->showLabel === true) {
            $this->template = "{label}\n" . $this->template;
        }
    }

    /**
     * @see yii\widgets\ActiveField::checkboxList()
     */
    public function checkboxList($items = [], $options = [])
    {
        $items = $this->guessItems($items);

        return parent::checkboxList($items, $options);
    }

    /**
     * @see yii\widgets\ActiveField::radioList()
     */
    public function radioList($items = [], $options = [])
    {
        $items = $this->guessItems($items);

        return parent::radioList($items, $options);
    }

    /**
     * @see yii\widgets\ActiveField::dropDownList()
     */
    public function dropDownList($items = [], $options = [])
    {
        $items = $this->guessItems($items);

        return parent::dropDownList($items, $options);
    }

    private function guessItems($items)
    {
        if (empty($items)) {
            $model = $this->model;
            $method = 'get' . Inflector::id2camel($this->attribute, '_') . 'Map';
            if (method_exists($model::className(), $method)) {
                return call_user_func([$model::className(), $method]);
            } else {
                throw new \yii\base\InvalidParamException("模型 {$model::className()} 中不存在 {$this->attribute} 字段的 map 方法！");
            }
        } else {
            return $items;
        }
    }

    /**
     * 下拉框提示输入框
     * 
     * @param  array  $url     提示请求url
     * @param  array  $options html属性
     * @return object
     */
    public function bindHint($url, $options = [])
    {
        $options['id'] = ArrayHelper::getValue($options, 'id', static::getInputId($this->model, $this->attribute));
        $options['autocomplete'] = 'off';

        Yii::$app->getView()->registerJs('$("#' . $options['id'] . '").bindHint("' . self::createUrl($url) . '")');

        return $this->textInput($options);
    }

    /**
     * 日期选择输入框
     *
     * @param  array   $options html属性
     * @return objecct
     */
    public function datepicker($options = [])
    {
        $options['class'] = (array) ArrayHelper::getValue($options, 'class', []);
        $options['class'][] = 'datepicker input-text';
        $options['onfocus'] = 'WdatePicker({dateFmt: "' . ArrayHelper::remove($options, 'fmt', 'yyyy-MM-dd') . '"})';
        $options['id'] = ArrayHelper::getValue($options, 'id', static::getInputId($this->model, $this->attribute));

        $view = Yii::$app->getView();
        \common\assets\DatePickerAsset::register($view);

        return $this->textInput($options);
    }

    /**
     * 时间选择输入框
     *
     * @param  array   $options html属性
     * @return objecct
     */
    public function timepicker($options = [])
    {
        $options['class'] = (array) ArrayHelper::getValue($options, 'class', []);
        $options['class'][] = 'timepicker input-text';
        $options['id'] = ArrayHelper::getValue($options, 'id', static::getInputId($this->model, $this->attribute));

        $view = Yii::$app->getView();
        \common\assets\TimePickerAsset::register($view);
        $view->registerJs('$("#' . $options['id'] . '").timepicker($.config("timepicker"))');

        return $this->textInput($options);
    }

    /**
     * 短信验证码输入框
     *
     * @param array $config  按钮事件的配置参数
     * @param array $options 输入框的html属性
     * @return object
     */
    public function verifyCode($config = [], $options = [])
    {
        $default = [
            'action' => url(['site/verifyCode']),
            'mobile' => '#' . Html::getInputId($this->model, 'mobile'),
            'captcha' => '#' . Html::getInputId($this->model, 'captcha')
        ];
        $config = array_merge($default, $config);
        $input = Html::activeTextInput($this->model, $this->attribute, $options);
        $button = Html::buttonInput('获取验证码', ['id' => 'verifyCodeBtn', 'data' => $config, 'class' => 'verifyCodeBtn']);
        $this->parts['{input}'] = $input . $button;

        Yii::$app->getView()->registerJs('$("#verifyCodeBtn").verifyCode()');

        return $this;
    }

    /**
     * 验证码输入框
     * 
     * @param  array  $options 输入框的html属性
     * @return object
     */
    public function captcha($options = [])
    {
        $default = [
            'captchaAction' => '/site/captcha',
            'template' => '{input} {image}',
            'options' => [
                'class' => 'form-control'
            ],
            'imageOptions' => [
                'alt' => '点击换图',
                'title' => '点击换图',
                'style' => 'cursor: pointer;vertical-align: bottom;'
            ]
        ];
        $options = array_merge($default, $options);

        return $this->widget('yii\captcha\Captcha', $options);
    }

    /**
     * 与 common\widgets\Upload 配套使用，输出定制化的fileInput方法
     * 
     * @param  array  $options html属性
     * @return object
     */
    public function upload($options = [])
    {
        if (strpos($this->attribute, '[]') !== false) {
            $attribute = explode('[]', $this->attribute)[0];
            $options['name'] = Html::getInputName($this->model, $attribute) . '[]';
        } else {
            $options['name'] = Html::getInputName($this->model, $this->attribute);
        }

        return $this->fileInput($options);
    }

    /**
     * 获取百度富文本编辑器
     * 
     * @param  array  $options 编辑器选项
     * @return object
     */
    public function editor($options = [])
    {
        $client['clientOptions'] = $options;

        return $this->widget('common\widgets\UEditor', $client);
    }

    /**
     * 获取 markdown 编辑器
     * 
     * @param  array  $options 编辑器选项
     * @return object
     */
    public function markdown($options = [])
    {
        return $this->widget('ijackua\lepture\Markdowneditor', $options);
    }
}
