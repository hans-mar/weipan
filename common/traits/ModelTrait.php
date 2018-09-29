<?php

namespace common\traits;

use Yii;
use common\helpers\ArrayHelper;

/**
 * ARModel和Model的共通方法
 *
 * @author ChisWill
 */
trait ModelTrait
{
    /**
     * 覆写父类方法，当未设置$data时，自动获取 POST 中的数据
     */
    public function load($data = null, $formName = null)
    {
        if ($data === null) {
            $data = Yii::$app->request->post();
        }
        return parent::load($data, $formName);
    }

    /**
     * 获取模型中字段定义的描述信息
     * 
     * @param  string $field 字段
     * @return string
     */
    public function label($field)
    {
        $labels = method_exists($this, 'attributeLabels') ? $this->attributeLabels() : [];

        return ArrayHelper::getValue($labels, $field, '');
    }
}
