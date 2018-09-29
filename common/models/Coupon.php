<?php

namespace common\models;

use Yii;

/**
 * 这是表 `coupon` 的模型
 */
class Coupon extends \common\components\ARModel
{
    public function rules()
    {
        return [
            [['desc', 'amount', 'valid_day'], 'required'],
            [['remark'], 'default', 'value' => ''],
            [['amount'], 'number'],
            [['coupon_type', 'valid_day'], 'integer'],
            [['desc'], 'string', 'max' => 50],
            [['amount', 'coupon_type'], 'unique', 'targetAttribute' => ['amount', 'coupon_type'], 'message' => 'The combination of 额度 and 优惠劵类型 has already been taken.']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'desc' => '描述',
            'remark' => '备注信息',
            'amount' => '额度',
            'coupon_type' => '优惠劵类型',
            'valid_day' => '有效时间（天）',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'coupon_type']);
    }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'coupon.id' => $this->id,
                'coupon.amount' => $this->amount,
                'coupon.coupon_type' => $this->coupon_type,
                'coupon.valid_day' => $this->valid_day,
            ])
            ->andFilterWhere(['like', 'coupon.desc', $this->desc])
            ->andFilterWhere(['like', 'coupon.remark', $this->remark])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/

    // Map method of field `coupon_type`
    public static function getCouponTypeMap($prepend = false)
    {
        $map = Product::find()->where(['state' => self::STATE_VALID])->map('id', 'name');

        return self::resetMap($map, $prepend);
    }

    // Format method of field `coupon_type`
    public function getCouponTypeValue($value = null)
    {
        return $this->resetValue($value);
    }
}
