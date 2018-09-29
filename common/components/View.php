<?php

namespace common\components;

use Yii;

/**
 * 视图层的基类
 *
 * @author ChisWill
 */
class View extends \yii\web\View
{
    use \common\traits\ChisWill;

    /**
     * 获取指定 bundle 的assetUrl
     * 
     * @param  string $bundle 指定bundle的类名
     * @return string
     */
    public function getAssetUrl($bundle)
    {
        return Yii::$app->assetManager->getBundle($bundle)->baseUrl;
    }

    /**
     * 从基类 yii\base\View 中提取该方法，目的是能在视图中使用 self::$user 语法来替代 static::$user
     * 
     * @see yii\base\View::renderPhpFile()
     */
    public function renderPhpFile($_file_, $_params_ = [])
    {
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        require($_file_);

        return ob_get_clean();
    }

    /**
     * 快速引入css文件
     * 
     * @param string $file css文件名
     */
    public function regCss($file)
    {
        if (strrchr($file, '.css') !== '.css') {
            $file .= '.css';
        }
        $file = '/css/' . $file;
        $file .= '?v=' . filemtime(Yii::getAlias('@webroot/' . $file));
        $this->registerCssFile($file, ['depends' => ['frontend\assets\AppAsset']]);
    }

    /**
     * 快速引入js文件，默认在<body>底部引入
     * 
     * @param string $file js文件名
     */
    public function regJs($file)
    {
        if (strrchr($file, '.js') !== '.js') {
            $file .= '.js';
        }
        $file = '/js/' . $file;
        $file .= '?v=' . filemtime(Yii::getAlias('@webroot/' . $file));
        $this->registerJsFile($file);
    }
}
