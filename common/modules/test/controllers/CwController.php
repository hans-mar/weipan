<?php

namespace common\modules\test\controllers;

use Yii;
use common\models\User;
use common\models\Test;
use common\models\AdminUser;
use common\helpers\Url;
use common\helpers\Json;
use common\helpers\Html;
use common\helpers\Inflector;
use common\helpers\FileHelper;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;

class Message
{
    public $email;

    public function __construct($a = 'abc', Email $email)
    {
        $this->email = $email;
    }

    public function send()
    {
        $this->email->send();
    }
}

/**
 * @author ChisWill
 */
class CwController extends \common\components\WebController
{
    public $layout = 'main';

    public function actionIndex()
    {
        $this->view->title = 'Test - ChisWill';

        return $this->render('index');
    }

    public function actionGetCaptcha()
    {
        $model = new User;

        return $this->render('captcha', compact('model'));
    }

    public function actionCharts()
    {
        return $this->render('charts');
    }

    public function actionRedis()
    {
        $redis = redis();

        // $redis->set('zkey', 345);
        // cache()->set('zkey', 23234);
        // var_dump(cache()->get('ddabc'));
        // $redis->set('abc', false);
        // var_dump($redis->get('zkey'));
        tes($redis->scan(0));
    }

    public function actionQuery()
    {
        $saleQuery = \bpm\models\OrderItem::find()
            ->limit(1)
            ;
        $purchaseQuery = clone $saleQuery;

        $query = $saleQuery
                    ->with(['sale.title', 'purchase.title'])
                    // ->limit(1)
                    ->andWhere(['order_type' => \bpm\models\OrderEvent::ORDER_TYPE_SALE])
                    ->union(
                 $purchaseQuery
                    ->andWhere(['order_type' => \bpm\models\OrderEvent::ORDER_TYPE_PURCHASE]));
        // $query = \bpm\models\OrderItem::find()->with(['sale.title', 'purchase.title'])->from(['sub' => $query])->limit(2);

        $res = $query->asArray()->all();
        foreach ($res as $key => $model) {
            tes($model);
        }

        return $this->render('index');
    }

    public function actionUploadFile()
    {
        $model = new \bpm\models\Test;
        $model->uid = rand(1, 1000000);

        $upload = self::getUpload('file');

        if ($model->load()) {
            if ($model->validate()) {
                $path = '';
                if (is_array($model->file)) {
                    foreach ($model->file as $key => $file) {
                        $file->move();
                        $path .= $file->filePath;
                    }
                } else {
                    $model->file->move();
                    $path = $model->file->filePath;
                }
                $model->path = $path;
                // test($model->file->realName,$model->file->filePath,$model->file->originName);
                if ($model->save(false)) {
                    return $this->redirect(['uploadFile']);
                }
            } else {
                // test($model->errors);
            }
            // if ($upload->validate()) {
            //     $upload->move();
            //     test($upload->realName,$upload->filePath,$upload->originName);
            //     // test($upload->filePath);
            // } else {
            //     test($upload->errors);
            // }
        }
        
        return $this->render('uploadFile', compact('model'));
    }

    public function actionJsCrop()
    {
        if (req()->isPost) {
            $image = post('image');
            list($info, $img) = explode(',', $image);
            $path = Yii::getAlias('@webroot' . config('uploadPath'));
            FileHelper::mkdir($path);
            file_put_contents($path . '/1.jpg', base64_decode($img));
            return success(config('uploadPath') . '/1.jpg');
        }

        return $this->render('jsCrop');
    }

    public function actionUploads()
    {
        if (req()->isPost) {
            $upload = self::getUpload();
            $upload->move();
        } else {
            return $this->render('upload');
        }
    }

    public function actionProcess()
    {
        $key = ini_get("session.upload_progress.prefix") . 'cw';

        if (!empty($_SESSION[$key])) {
            $current = $_SESSION[$key]["bytes_processed"];
            $total = $_SESSION[$key]["content_length"];
            echo $current < $total ? ceil($current / $total * 100) : 100;
        } else {
            echo 100;
        }
    }

    public function actionRbac()
    {
        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissions();
        tes($permissions);
    }

    public function actionDi()
    {
        Yii::$container->set('common\modules\test\controllers\Message', 'email\Email');
        $message = Yii::createObject('common\modules\test\controllers\Message');
        $message->send();
        $message->send();
    }

    public function actionTree()
    {
        $query = AdminUser::find()->where('state=1 and is_trader=1')->with('department');
        $html = $query->getTree(['withName' => ['code' => 'name'], 'withOptions' => ['disabled' => 'disabled']])->dropDownList('realname');

        $query2 = AdminMenu::find();
        $html2 = $query2->getTree([
            'beforeItem' => function ($row) {
                return $row['status'] == AdminMenu::STATUS_ACTIVE;
            },
            'select' => ['id', 'pid', 'name'],
            'optionAttrs' => ['path']
        ])->dropDownList('name');

        $query3 = AdminDepartmentPositionRel::find()->where('user.state=1 and user.is_trader=1')->andWhere(['in', 'admin_id', AdminUser::getSubAdmins()])->with('department')->joinWith('user');
        $html3 = $query3->getTree([
            'withName' => 'name', 
            'withQuery' => function ($query) {
                $subQuery = Department::find()->select(['pid', 'code'])->where(['in', 'id', AdminDepartmentPositionRel::find()->select('department_id')->where('admin_id = ' . u('id'))]);
                $query
                    ->innerJoin(['sub' => $subQuery])
                    ->where('id = sub.pid')
                    ->union(
                Department::find()
                    ->innerJoin(['sub' => $subQuery])
                    ->where(['like', 'department.code', self::dbExpression('CONCAT(sub.code, "%")')]));
            }, 
            'withOptions' => ['disabled' => 'disabled']
            ])
        ->dropDownList('user.realname');

        return $this->render('tree', compact('html', 'html2', 'html3'));
    }

    public function actionPower()
    {
        $query = Order::find()->joinWith(['supplyUser', 'demandUser'])->children(['supplyUser', 'demandUser', 'supplyUser'])->limit(10);
        $users = $query->asArray()->count();
        test($query->rawsql, $users);
    }

    public function actionSocket()
    {
        return $this->render('socket');
    }

    public function actionModel()
    {
        $inbox = new \common\modules\bpm\models\Inbox;

        if (req()->isPost) {
            $inbox->tid = $_POST['tid'];
            $inbox->admin_id = mt_rand(1,100);
            if (!$inbox->save()) {
                return self::error($inbox->getErrors());
                // test($inbox->getErrors());
            } else {
                return self::success($inbox->id);
                // $this->redirect('index');
            }
        }

        return $this->render('model', compact('inbox'));
    }

    public function actionTable()
    {
        \common\assets\HuiAsset::register($this->view);
        $article = new \common\modules\manual\models\Article;
        // $query = $order::find()->orderBy(self::dbExpression('case when order.id > :a then :b else :c', [':a' => 1000, ':b'=>1,':c'=>2]));
        
        $query = $article->search()->orderBy('article.id');
        // ->orderBy(self::dbExpression('case when order.id > 1010 then :b else :c end asc', [':b'=>1,':c'=>2]));
        // $data = $query->rawsql;
        // test($data);
        // test(self::dbExpression('case when order.id > :a then :b else :c', [':a' => 1000, ':b'=>1,':c'=>2]));

        $html = $query->getTable([
            ['type' => 'checkbox'],
            'id',
            'menu_id',
            'state',
            ['type' => ['edit' => 'edit-one', 'view', 'reset', 'delete']]
        ], [
            'searchColumns' => [
                'id',
                'menu_id'
            ],
            // 'tableOptions' => ['id' => 't1','class' => 'table']
        ]);

        $html2 = '';

        $menu = new \common\modules\manual\models\Menu;
        $query = $menu->search();

        $html2 = $query->getTable([
            'id',
            'name',
            'pid',
            'created_at',
            ['type' => ['reset']]
        ], [
            // 'tableOptions' => ['id' => 't2','class' => 'table']
        ]);

        return $this->render('table', compact('query', 'html', 'html2'));
    }

    public function actionDatatable()
    {
        return $this->render('datatable');
    }

    public function actionJstable()
    {
        $model = new Test;
        
        $query = $model::find()->limit(35)->orderBy('id desc');

        $html = $query->getTable([
            'id' => ['sort' => true],
            'created_by',
            'state',
            'created_at' => function($value) {
                return date('Y-m-d', strtotime($value->created_at));
            },
        ], [
            'searchColumns' => [
                'id',
                'state' => 'radio',
            ],
            'paging' => false,
            'sort' => false,
            'tableOptions' => ['id' => 't1', 'class' => 'table']
        ]);

        $html2 = '';

        $query = \common\models\AdminUser::find()->where('power <= 4');

        $html2 = $query->getTable([
            'id',
            'username',
            'realname' => ['type' => 'text'],
            'created_at',
        ], [
            'paging' => false,
            'sort' => false,
            'tableOptions' => ['id' => 't2','class' => 'table']
        ]);

        return $this->render('jstable', compact('html', 'html2'));
    }

    public function actionLinkage()
    {
        $query = Department::find()->where('code like "1-%"');

        $html = $query->getLinkage([
            'id',
            'name' => ['type' => 'text'],
            'code',
            'pid',
            'level' => ['type' => 'toggle']
        ]);

        return $this->render('linkage', compact('html'));
    }

    public function actionSort()
    {
        // $user = new User(get('User'));
        // $res = $user->search()->all();
        // $res = User::findBySql('select * from {{user}} where id = 2')->one();

        // $query1 = Product::findBySql('select * from {{product}} where id > 12');
        // $query2 = 'select * from {{user}} where id > 12';

        $query1 = Product::find()->where('id > 12');
        $data1 = $query1->order('id desc', ['id', 'product_name'], ['sortParam' => 'sort1'])->paginate();
        $sort1 = $query1->getSort();
        
        $query2 = User::find()->where('id > 12');
        $sort2 = new \yii\data\Sort([
            'attributes' => [
                'id',
                'name' => [
                    'asc' => ['name' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC],
                    'default' => SORT_DESC,
                    'label' => 'name',
                ],
            ],
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'sortParam' => 'sort2'
        ]);
        $data2 = $query2->orderBy($sort2)->paginate();
        // $data1 = self::paginate($query1);
        // $data2 = self::paginate($query2);
        $query3 = \common\models\User::find();

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query3,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('sort', [
            'data1' => $data1,          
            'data2' => $data2,
            'sort1' => $sort1,
            'sort2' => $sort2,
            'dataProvider' => $dataProvider
        ]);
    }
}

namespace email;

class Email
{
    private $count = 0;

    public function send()
    {
        echo ++$this->count . ' feng<br>';
    }
}
