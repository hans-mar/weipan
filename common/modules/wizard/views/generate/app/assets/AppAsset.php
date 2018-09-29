<?php
echo "<?php\n";
?>

namespace <?= $namespace ?>;

/**
 * <?= $appName ?> 基础静态资源
 */
class AppAsset extends \common\components\AssetBundle
{
    public $js = [
        'js/site.js'
    ];
    public $css = [
        'css/site.css'
    ];
    public $depends = [
        'common\assets\CommonAsset'
    ];
}
