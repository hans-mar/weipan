<?php

namespace common\modules\test\controllers;

class SiteController extends \common\components\WebController
{
    public $layout = 'main';

    public function actionIndex()
    {
        $this->view->title = 'Test - ChisWill';

        return $this->render('index');
    }
}