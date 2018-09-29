<?php

namespace admin\controllers;

use Yii;
use admin\models\AdminUser;
use admin\models\LoginForm;
use admin\models\Retail;
use admin\models\AdminLeader;

/**
 * @author ChisWill
 */
class SiteController extends \admin\components\Controller
{
    public function actionIndex()
    {
        $this->layout = 'main';

        $this->view->title = config('web_name') ? config('web_name') . ' - 管理系统' : '';

        return $this->render('index');
    }

    public function actionProfile()
    {
        return $this->render('profile');
    }

    public function actionUserInfo()
    {
        if (u()->power == AdminUser::POWER_MANAGER) {
            $model = Retail::find()->where(['account' => u()->username])->one();
        } else {
            $model = AdminLeader::findOne(u()->id);
        }

        return $this->render('userInfo', compact('model'));
    }

    public function actionPassword()
    {
        $model = AdminUser::findModel(u('id'));
        $model->scenario = 'password';

        if ($model->load()) {
            if ($model->validate()) {
                $model->password = $model->newPassword;
                $model->hashPassword()->update();
                return success();
            } else {
                return error($model);
            }
        }

        return $this->renderPartial('password', compact('model'));
    }

    public function actionWelcome()
    {
        return $this->render('welcome');
    }

    public function actionLogin()
    {
        
        $this->view->title = '登录 - 管理系统';

        $model = new LoginForm;

        if ($model->load()) {
            if ($model->login()) {
                return $this->redirect(['index']);
            } else {
                return error($model);
            }
        }

        return $this->renderPartial('loginnew', compact('model'));
        //return $this->render('login', compact('model'));
    }

    public function actionLogout()
    {
        user()->logout(false);

        return $this->redirect(['login']);
    }
}
