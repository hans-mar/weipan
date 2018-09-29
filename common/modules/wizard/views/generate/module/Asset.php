<?php
echo "<?php\n";
?>

namespace <?= $namespace ?>;

/**
 * <?= $moduleName ?> 静态资源
 */
class Asset extends \common\components\AssetBundle
{
    public $sourcePath = '<?= $sourcePath ?>';
    public $js = [
        'main.js'
    ];
    public $css = [
        'main.css'
    ];
    public $depends = [
        'common\assets\CommonAsset'
    ];
}
