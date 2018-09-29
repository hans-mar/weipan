<?php

namespace admin\assets;

/**
 * @author ChisWill
 */
class FrameAsset extends \common\components\AssetBundle
{
    public $depends = [
        'admin\assets\MainAsset',
        'common\assets\JqueryFormAsset'
    ];
}
