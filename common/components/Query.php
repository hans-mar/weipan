<?php

namespace common\components;

/**
 * 一般查询的基类
 *
 * @author ChisWill
 */
class Query extends \yii\db\Query
{
    use \common\traits\QueryTrait;
}
