<?php
echo "<?php\n";
?>

namespace <?= $namespace ?>;

use Yii;
use <?= $appName ?>\models\User;

class UserController extends \back\components\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionPassword()
    {
        $this->view->title = '修改密码';

        $model = User::findOne(u('id'));
        $model->scenario = 'password';

        if ($model->load()) {
            if ($model->validate()) {
                $model->password = $model->newPassword;
                if ($model->hashPassword()->update()) {
                    return $this->redirect(['index']);
                } else {
                    goto error;
                }
            } else {
                error:
                // return self::error($model);
            }
        }

        return $this->render('password', compact('model'));
    }
}
