<?php

namespace common\widgets;

use common\assets\UEditorAsset;
use common\components\View;
use common\helpers\Json;
use common\helpers\ArrayHelper;

/**
 * 百度富文本编辑器
 * 
 * @author ChisWill
 */
class UEditor extends \kucha\ueditor\UEditor
{
    public function init()
    {
        $defaultOptions = [
            //编辑区域大小
            'initialFrameHeight' => '300',
            'elementPathEnabled' => false,
            'enableAutoSave' => false,
            'wordCount' => false,
        ];
        // 默认的菜单
        $toolbars = [
            [
                'fullscreen', 'undo', 'redo', '|', 'fontsize',
                'bold', 'italic', 'underline', '|',
                'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|',
                'forecolor', 'backcolor', '|',
                'simpleupload', 'emotion', 
                
            ],
        ];
        $this->clientOptions = ArrayHelper::merge($defaultOptions, $this->clientOptions);
        $this->clientOptions['toolbars'] = ArrayHelper::getValue($this->clientOptions, 'toolbars', $toolbars);
        parent::init();
    }

    /**
     * 注册客户端脚本
     */
    protected function registerClientScript()
    {
        UEditorAsset::register($this->view);
        $clientOptions = Json::encode($this->clientOptions);
        $script = "UE.getEditor('" . $this->id . "', " . $clientOptions . ")";
        $this->view->registerJs($script, View::POS_READY);
    }
}
