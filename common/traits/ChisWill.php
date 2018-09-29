<?php

namespace common\traits;

/**
 * 统一引入所有必要的 trait
 *
 * @author ChisWill
 */
trait ChisWill
{
    use DbTrait;
    use FuncTrait;

    public static $date; // 当前日期
    public static $time; // 当前时间
}
