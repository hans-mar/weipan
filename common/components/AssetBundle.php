<?php

namespace common\components;

use Yii;
use common\helpers\FileHelper;

/**
 * 前端资源包的基类
 *
 * @author ChisWill
 */
class AssetBundle extends \yii\web\AssetBundle
{
    use \common\traits\FuncTrait;

    public function init()
    {
        parent::init();
        // 当静态资源放在web可见的目录时，自动在文件末尾添加版本号
        if (!$this->sourcePath) {
            $basePath = Yii::getAlias('@' . FileHelper::getCurrentApp() . '/web/');
            $themePath = THEME_NAME === null ? '' : Yii::getAlias('@web/themes/' . THEME_NAME . '/');
            // js文件
            foreach ($this->js as $key => $js) {
                $this->js[$key] .= '?v=' . filemtime($basePath . $js);
            }
            // css文件
            foreach ($this->css as $key => $css) {
                $cssFile = $basePath . ltrim($themePath, '/') . $css;
                if (!file_exists($cssFile)) {
                    $cssPath = $css;
                    $cssFile = $basePath . $css;
                } else {
                    $cssPath = $themePath . $css;
                }
                $this->css[$key] = $cssPath . '?v=' . filemtime($cssFile);
            }
        }
    }
}
