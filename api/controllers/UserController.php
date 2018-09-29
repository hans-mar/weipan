<?php
namespace api\controllers;

use Yii;
use common\helpers\Html;
use common\helpers\FileHelper;
use common\helpers\ArrayHelper;
use api\models\User;
use yii\filters\auth\QueryParamAuth;

class UserController extends \api\components\Controller
{
    public $modelClass = 'api\models\User';

    public function init()
    {
        parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
        ];
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
        return $behaviors;
    }

    public function actionIndex()
    {
        return 'this is GET, list all'.Yii::$app->user->id;
        empty($_GET['page']) && $_GET['page'] = 10;
        empty($_GET['sort']) && $_GET['sort'] = '';
        $page = (int) $_GET['page'];
        $sort = $_GET['sort'];
        return \api\models\User::find()->getData($page, $sort);
    }

    public function actionDelete($id)
    {
        return 'this is DELETE,id =' .$id;
    }

    public function actionView($id)
    {
        return 'this is GET, id='.$id;
        return User::findOne($id);
    }

    public function actionCreate()
    {
        return 'this is POST' . serialize($_POST);
    }

    public function actionOptions($id = '')
    {
        return 'this is OPTIONS, id:' . $id;
    }

    public function actionUpdate($id)
    {
        $data = serialize($_FILES);
        return 'this is PUT, id:' . $id . 'data:'.$data;
    }
}
