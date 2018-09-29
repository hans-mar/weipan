<?php

namespace frontend\controllers;

use Yii;
use frontend\models\User;
use frontend\models\UserExtend;
use frontend\models\UserRebate;
use frontend\models\Product;
use frontend\models\Order;
use frontend\models\ProductPrice;
use frontend\models\DataAll;
use frontend\models\UserCharge;
use frontend\models\Retail;
use frontend\models\AdminUser;
use common\helpers\FileHelper;

class ManController extends \frontend\components\Controller
{
    public function beforeAction($action)
    {
		// var_dump('ddd');
        // exit();
        $actions = ['card', 'register', 'my-code'];
        //$actions = ['ajax-update-status', 'wxtoken', 'wxcode', 'test', 'rule', 'captcha','notify', 'hx-weixin', 'zynotify', 'update-user', 'update', 'tynotify','login','reg','verify-code','ajax-reg','ajax-forget','pass-for-get'];
        //        var_dump(user()->isGuest);
        //        var_dump(!in_array($this->action->id, $actions));
        // exit;
           if(user()->isGuest && !in_array($this->action->id, $actions))
           {
               $this->redirect(['/site/login']);
               return false;
           }
           else{
               return true;


           }
        
        // if (user()->isGuest && !in_array($this->action->id, $actions)) {
        //     $wx = session('wechat_userinfo');
        //     if (!empty($wx)) {
        //         $user = User::find()->where(['open_id' => $wx['openid']])->one();
        //         $user->login(false);
        //     } else {
        //         $code = get('code');
        //         if (empty($code)) {
        //             $this->redirect(['/man.php']);
        //             return false;
        //         } else {
        //             User::registerUser($code);
        //         }
        //     }
        // }
        // if (!parent::beforeAction($action)) {
        //     return false;
        // } else {
        //     return true;
        // }
    }

    public function actionIndex()
    {
		var_dump('ddd');
        exit();
        $this->view->title = '经纪人';  
        $extend = UserExtend::findOne(u()->id);
        if (empty($extend)) {
            return $this->redirect(['manager/register']);
        }
        //直属客户
        $idArr = User::getUserOfflineId();
        $userNum = count($idArr[0]);
        $orderNum = Order::find()->where(['order_state' => Order::ORDER_THROW])->andWhere(['in', 'user_id', $idArr[0]])->count();
        //分享支付
        // require Yii::getAlias('@vendor/wx/WxTemplate.php');
        // $wxTemplate = new \WxTemplate();

        // $wxConfig = $wxTemplate->getWxConfig('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        return $this->render('index', compact('extend', 'userNum', 'orderNum', 'wxConfig'));
    }

    public function actionIncome()
    {
        $this->view->title = '收入'; 
        $model = new UserRebate();
        $query = $model->managerTimeQuery();

        $data = $query->paginate(PAGE_SIZE);
        $count = $query->totalCount;

        $extend = UserExtend::findModel(u()->id);
        return $this->render('income', compact('extend', 'data', 'count', 'model'));
    }

    public function actionCustomer()
    {
        $this->view->title = '直属客户';  

        $idArr = User::getUserOfflineId();
        $userNum = count($idArr[0]);

        $model = new User();
        $query = $model->customerQuery($idArr[0]);
        $data = $query->paginate(PAGE_SIZE);
        $count = $query->totalCount;

        return $this->render('customer', compact('data', 'count', 'model', 'userNum'));
    }
    
    public function actionCover()
    {
        $this->view->title = '客户平仓';  
        $idArr = User::getUserOfflineId();
        $userNum = count($idArr[0]);

        $model = new Order();
        $query = $model->coverQuery($idArr[0]);
        $data = $query->paginate(PAGE_SIZE);
        $count = $query->totalCount;
        $order = Order::find()->where(['order_state' => Order::ORDER_THROW])->andWhere(['in', 'user_id', $idArr[0]])->select('SUM(hand) as hand, SUM(fee) as fee')->one();
        //当前可用产品
        $productArr = Product::getProductArray();

        return $this->render('cover', compact('data', 'count', 'model', 'order', 'productArr'));
    }

    public function actionCard()
    {
        $this->view->title = '我的名片';

        $filePath = Yii::getAlias('@webroot/' . config('uploadPath') . '/images/');
        FileHelper::mkdir($filePath);
        $src = $filePath . 'code_' . u()->id . '.jpg';
        
        if (!file_exists($src)) {
            $url = UserExtend::getManagerCodeImg();
            // $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQEM8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL2lqLUxYeHJsNGVxWUxSd0p1Uk1JAAIE3A0nWAMEAAAAAA==';
            $data = file_get_contents($url);
            $src = $filePath . 'code_' . u()->id . '.jpg';
            file_put_contents($src, $data);
        } else {
            require Yii::getAlias('@vendor/wx/WxTemplate.php');
        }
        $src = config('uploadPath') . '/images/' . 'code_' . u()->id . '.jpg';
        
        if (YII_DEBUG) {
            $wxConfig = ['timestamp' => '', 'noncestr' => '', 'signature' => ''];
        } else {
            //分享支付
            $wxTemplate = new \WxTemplate();
            $wxConfig = $wxTemplate->getWxConfig('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        }

        return $this->render('card', compact('src', 'wxConfig'));
    }
    //分享图片地址
    public function actionMyCode($id)
    {
        $this->view->title = '我的名片';

        $user = User::findModel($id);
        if ($user->is_manager != User::IS_MANAGER_YES) {
            return false;
        }
        $filePath = Yii::getAlias('@webroot/' . config('uploadPath') . '/images/');
        FileHelper::mkdir($filePath);
        $src = $filePath . 'code_' . $id . '.jpg';
        $img = config('uploadPath') . '/images/' . 'code_' . $id . '.jpg';
        if (!file_exists($src)) {
            $img = '/images/' . 'my_qrcode.jpg';
        }
        //分享支付
        require Yii::getAlias('@vendor/wx/WxTemplate.php');
        $wxTemplate = new \WxTemplate();

        $wxConfig = $wxTemplate->getWxConfig('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        return $this->render('myCode', compact('img', 'wxConfig', 'user'));
    }

    public function actionRegister()
    {
        $this->view->title = '经纪人';
		var_dump('ddd');
		
		exit();
        if (u()->is_manager == User::IS_MANAGER_YES) {
            $this->redirect(['manager/index']);
        }
        $user = User::findModel(u()->id);
        //经纪人表
        $userExtend = UserExtend::find()->where(['user_id' => u()->id])->one();
        if (!empty($userExtend)) {
            $userExtend->scenario = 'register';
        } else {
            $userExtend = new UserExtend(['scenario' => 'register']);
        }
        if ($userExtend->load(post())) {
            if (u()->apply_state == User::APPLY_STATE_WAIT) {
                $this->redirect(['site/index']);
            }
            //请输入邀请码
            $retail = Retail::find()->where(['code' => $userExtend->coding])->one();
            if (empty($retail)) {
                return error('邀请码不正确!');
            }
            //代理商的admin_id
            $adminUser = AdminUser::find()->where(['username' => $retail->account])->one();
            if (empty($adminUser)) {
                return error('此代理商已经不存在了!');
            }
            $userExtend->user_id = u()->id;
            $userExtend->coding = $adminUser->id;
            if ($userExtend->validate()) {
                if (!empty($userExtend)) {
                    $userExtend->save(false);
                } else {
                    $userExtend->insert(false);
                }
                $user = User::findModel(u()->id);
                $user->apply_state = User::APPLY_STATE_WAIT;
                $user->admin_id = $adminUser->id;
                $user->update();
                return success(url(['site/index']));
            } else {
                return error($userExtend);
            }
        }
        //微信注册用户
        // User::registerUser(get('code'));
        $userExtend->coding = '';

        return $this->render('register', compact('user', 'userExtend'));
    }

    //微信token验证
    public function actionOut()
    {
        u()->logout(false);
        test(1);
    } 
}
