<?php

namespace common\components;

use Yii;

/**
 * 模块的基类，增加默认路由，并且增加是否需要登录才能访问的功能
 *
 * @author ChisWill
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    use \common\traits\ChisWill;
    
    /**
     * @var boolean 是否必须登录
     */
    public $loginRequired = true;
    /**
     * @var string 登录页面action
     */
    public $loginAction = 'login';
    /**
     * @var string 验证码action
     */
    public $captchaAction = 'captcha';

    public function bootstrap($app)
    {
        if (!($app instanceof \yii\web\Application)) {
            return false;
        }

        $app->getUrlManager()->addRules([
            $this->id => $this->id . '/site/index',
            $this->id . '/?' => $this->id . '/site/index'
        ], false);
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        } elseif (YII_ENV_PROD && !in_array($this->id, config('accessModule'))) {
            return false;
        }

        $nowAction = $action->id;
        if ($this->loginRequired === true && user()->isGuest && $nowAction != $this->loginAction && $nowAction != $this->captchaAction) {
            user()->loginRequired();
            return false;
        } else {
            return true;
        }
    }
}
