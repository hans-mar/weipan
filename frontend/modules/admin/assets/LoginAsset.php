<?php

namespace admin\assets;

/**
 * @author ChisWill
 */
class LoginAsset extends \common\components\AssetBundle
{
    public $sourcePath = '@admin/static/login';
    public $css = [
        'login.css'
    ];
    public $depends = [
        'common\assets\JqueryFormAsset'
    ];
}
