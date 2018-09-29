<?php
echo "<?php\n";
?>

namespace <?= $namespace ?>;

/**
 * 会员中心基础静态资源
 */
class AppAsset extends \common\components\AssetBundle
{
    public $js = [
        'js/user.js'
    ];
    public $css = [
        'css/user.css'
    ];
    public $depends = [
        'common\assets\CommonAsset'
    ];
}
