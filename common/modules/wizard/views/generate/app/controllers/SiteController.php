<?php
echo "<?php\n";
?>

namespace <?= $namespace ?>;

use Yii;
use <?= $appName ?>\models\User;

class SiteController extends \<?= $appName ?>\components\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionRegister()
    {
        $this->view->title = '注册';

        $model = new User(['scenario' => 'register']);

        if ($model->load()) {
            if ($model->validate()) {
                if ($model->hashPassword()->insert() && $model->login(false)) {
                    return $this->redirect(['index']);
                } else {
                    goto error;
                }
            } else {
                error:
                // return self::error($model);
            }
        }

        return $this->render('register', compact('model'));
    }

    public function actionLogin()
    {
        $this->view->title = '登录';

        if (!user()->isGuest) {
            return $this->redirect(['index']);
        }

        $model = new User(['scenario' => 'login']);

        if ($model->load()) {
            if ($model->login()) {
                return $this->goBack();
            } else {
                // return self::error($model);
            }
        }

        return $this->render('login', compact('model'));
    }

    public function actionLogout()
    {
        user()->logout(false);

        return $this->redirect(['index']);
    }
}
