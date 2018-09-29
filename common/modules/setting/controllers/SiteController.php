<?php

namespace common\modules\setting\controllers;

use Yii;
use common\helpers\FileHelper;
use common\helpers\ArrayHelper;
use common\modules\setting\models\Setting;

/**
 * @author ChisWill
 */
class SiteController extends \common\components\WebController
{
    public $layout = 'main';

    protected $app;

    public function init()
    {
        parent::init();

        $this->app = FileHelper::getCurrentApp();

        $this->view->title = $this->app . '设定 - ChisWill';
    }

    public function actionIndex()
    {
        if (req()->isAjax) {
            return $this->ajaxDisplay();
        } else {
            return $this->render('index', ['settings' => Setting::getWebSettings()]);
        }
    }

    protected function ajaxDisplay()
    {
        $settings = Setting::getWebSettings(true);

        $nowTopId = get('nowTopId');

        return self::success($this->renderAjax('_webSetting', compact('settings', 'nowTopId')));
    }

    public function actionAddSetting()
    {
        $setting = new Setting(['scenario' => post('addSetting') ? 'addSetting' : Setting::SCENARIO_DEFAULT]);
        $setting->load();
        if ($setting->validate()) {
            $setting->add();
            if (!empty($_POST['refresh'])) {
                return $this->ajaxDisplay();
            } else {
                return self::success($setting->id);
            }
        } else {
            return self::error($setting);
        }
    }

    public function actionSaveSetting()
    {
        $setting = new Setting;

        if ($setting->save()) {
            return self::success($setting->uploads);
        } else {
            return self::error($setting);
        }
    }

    public function actionDeleteSetting()
    {
        $setting = new Setting;

        $setting->delete(post('id'));

        return $this->ajaxDisplay();
    }
}
