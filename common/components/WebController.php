<?php

namespace common\components;

use Yii;
use common\helpers\Security;
use common\helpers\Inflector;
use common\helpers\FileHelper;

/**
 * 前后台控制器的基类
 *
 * @author ChisWill
 */
class WebController extends \yii\web\Controller
{
    use \common\traits\ChisWill;

    /**
     * @var boolean 是否允许表单重复提交
     */
    public $enableRepeatSubmit = false;
    /**
     * @var string  记录表单提交token的session名
     */
    protected static $formTokenSession = '_formToken';

    public function init()
    {
        parent::init();
        // 当非生产环境下的操作
        if (!YII_ENV_PROD) {
            // 开发环境允许重复提交
            if (YII_ENV_DEV) {
                $this->enableRepeatSubmit = true;
            }
            // 将自动为当前项目创建assets目录（如果不存在的话）
            $app = FileHelper::getCurrentApp();
            $assetsPath = Yii::getAlias('@' . $app) . '/web/assets';
            if (!file_exists($assetsPath)) {
                FileHelper::mkdir($assetsPath);
            }
            // 检测非法的控制器名
            if ($this->module instanceof \yii\web\Application) {
                if (in_array($this->id, FileHelper::getDirs(Yii::getAlias('@webroot')))) {
                    throw new \yii\base\Exception("{$this::className()} 使用了非法的控制器名，请修改！");
                }
            }
            // 检测非法命名的action
            $actions = array_keys($this->actions());
            array_walk($actions, function ($action) {
                $method = 'action' . ucfirst($action);
                if (method_exists($this, $method)) {
                    throw new \yii\base\Exception("{$this::className()}::{$method}() 使用了内置的action命名，请更改该方法的命名！");
                }
            });
        }
    }

    public function beforeAction($action)
    {
        $allowActions = ['notify', 'index', 'ajax-update-status', 'wxtoken', 'wxcode', 'card', 'test', 'hx-weixin', 'zynotify', 'tynotify','yfnotify','synotify'];

        if (in_array($action->id, $allowActions)) {
            $this->enableCsrfValidation = false;
        } else {
            $this->enableCsrfValidation = true;
        }

        if (parent::beforeAction($action)) {
            if ($this->enableRepeatSubmit !== true && req()->getIsAjax() && req()->getIsPost()) {
                // 绑定模型验证事件（必须使用 yii\base\Model::validate() 方法验证表单）
                \yii\base\Event::on(Model::className(), Model::EVENT_AFTER_VALIDATE, function ($event) {
                    // 当验证失败则移除session中表单的提交记录
                    if ($event->sender->hasErrors()) {
                        session(self::$formTokenSession, null);
                    }
                });
                // 校验是否反复点击按钮导致的表单重复提交
                if ($this->isRepeatSubmit()) {
                    self::ajaxReturn(false, '已经提交过了！');
                    return false;
                }
                // 记录表单提交的token
                $this->recordSubmit();
            }
            return true;
        } else {
            return false;
        }
    }

    public function actions()
    {
        return [
            // 不使用内置的错误处理action
            // 'error' => [
            //     'class' => 'yii\web\ErrorAction'
            // ],
            'generate' => [
                'class' => 'common\actions\GenerateAction'
            ],
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'backColor' => 0xFFFFFF,
                'minLength' => 4,
                'maxLength' => 4,
                'transparent' => true,
                'height' => 40,
                'width' => 80,
                'testLimit' => 0,
                'fixedVerifyCode' => YII_ENV_PROD ? null : '123',
            ],
        ];
    }

    /**
     * 泛用性用户产生型错误处理
     * ps. 当抛出的异常继承自 yii\base\UserException ，则不管 YII_DEBUG 为何值，函数调用栈信息都不会显示，
     *     这是因为这种错误会被认为是用户产生的错误，开发人员不需要去修正。
     */
    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;

        $message = $exception->getMessage();

        $name = $exception->getName();

        if (req()->isAjax) {
            return self::error($message);
        } else {
            $response = res();
            $response->statusCode = 200;
            try {
                // 在对应的视图文件中，使用语句 `$this->context->layout = 'error';` 来改变默认的layout
                $response->data = $this->render('error', compact('name', 'message', 'exception'));
            } catch (\yii\base\InvalidParamException $e) {
                $response->data = $message;
            }
            return $response->send();
        }
    }

    /**
     * common\widget\Table 组件生成表格的单元格快捷修改方法
     */
    public function actionAjaxUpdate()
    {
        $params = post('params');

        try {
            // 兼容 common\widget\Linkage 的参数形式，来获取模型类名
            if ($linkageParams = Security::base64decrypt($params['model'])) {
                $className = unserialize($linkageParams)['model'];
            } else {
                $className = $params['model'];
            }
            $model = $className::findOne($params['key']);
            $model->$params['field'] = $params['value'];
            if ($model->update()) {
                return self::success();
            } else {
                return self::error($model);
            }
        } catch (\Exception $e) {
            throwex($e);
        }
    }

    /**
     * common\widget\Table 组件生成表格的单元格批量删除方法
     * 将会判断当前表是否具有逻辑有效字段 state 来进行删除操作
     */
    public function actionDeleteAll()
    {
        if (!req()->isPost) {
            throwex('错误的请求方法');
        }
        $list = post('list');
        $model = post('model');

        if ($list) {
            try {
                $model = new $model;
                $key = current($model->primaryKey());
                if (in_array('state', $model->attributes())) {
                    $updateMap = ['state' => $model::STATE_INVALID];
                    if (in_array('updated_at', $model->attributes())) {
                        $updateMap['updated_at'] = self::$time;
                    }
                    if (in_array('updated_by', $model->attributes())) {
                        $updateMap['updated_by'] = u('id');
                    }
                    $ret = $model::updateAll($updateMap, [$key => $list]);
                } else {
                    $ret = $model::deleteAll([$key => $list]);
                }
                if ($ret) {
                    return self::success();
                } else {
                    return self::error('已经删除了');
                }
            } catch (\Exception $e) {
                throwex($e);
            }
        }
    }

    /**
     * common\widget\Table 组件默认的删除方法
     * 将会判断当前表是否具有逻辑有效字段 state 来进行删除操作
     */
    public function actionDelete()
    {
        if (!req()->isPost) {
            throwex('错误的请求方法');
        }
        $id = post('id');
        $model = post('model');

        $model = $model::findOne($id);
        if (in_array('state', $model->attributes())) {
            $model->state = $model::STATE_INVALID;
            $method = 'update';
        } else {
            $method = 'delete';
        }
        if ($model->$method()) {
            return self::success();
        } else {
            return self::error($model);
        }
    }

    /**
     * common\widget\Linkage 组件切换字段逻辑值的快捷方法
     */
    public function actionToggleLinkageItem()
    {
        $params = post('params');

        try {
            $linkageParams = Security::base64decrypt($params['params']);
            $className = unserialize($linkageParams)['model'];
            $model = $className::findOne($params['key']);
            // 此处的 1 是和 JS 端的请求值相对应的
            if ($params['value'] == 1) {
                $value = $model::STATE_VALID;
            } else {
                $value = $model::STATE_INVALID;
            }
            $model->$params['field'] = $value;
            if ($model->update()) {
                return self::success();
            } else {
                return self::error($model);
            }
        } catch (\Exception $e) {
            throwex($e);
        }
    }

    /**
     * common\widget\Linkage 组件的排序方法
     */
    public function actionSortLinkageItem()
    {
        try {
            return self::success(\common\widgets\Linkage::sortItem());
        } catch (\Exception $e) {
            throwex($e);
        }
    }

    /**
     * common\widget\Linkage 组件的添加元素方法
     */
    public function actionAddLinkageItem()
    {
        try {
            list($state, $info) = \common\widgets\Linkage::addItem();
            if ($state === true) {
                return self::success('', $info);
            } else {
                return self::error($info);
            }
        } catch (\Exception $e) {
            throwex($e);
        }
    }

    /**
     * common\widget\Linkage 组件的删除元素方法
     */
    public function actionDeleteLinkageItem()
    {
        try {
            $ret = \common\widgets\Linkage::deleteItem();
            if ($ret === true) {
                return self::success();
            } else {
                return self::error($ret);
            }
        } catch (\Exception $e) {
            throwex($e);
        }
    }

    /**
     * Ajax的成功返回
     * 
     * @param  string|array $info 返回的消息
     * @param  mixed        $data 附加的数据
     * @return object
     */
    public static function success($info = '', $data = null)
    {
        return static::ajaxReturn(true, $info, $data);
    }

    /**
     * Ajax的成功返回
     *
     * @param string|array|yii\base\model $info 如果info传入的是 yii\base\model 则表示输出该模型的错误信息
     * @param  mixed                      $data 附加的数据
     * @return object
     */
    public static function error($info = '', $data = null)
    {
        if ($info instanceof \yii\base\model) {
            $info = $info->getErrors();
        }
        // 清除表单重复提交认证
        session(self::$formTokenSession, null);

        return static::ajaxReturn(false, $info, $data);
    }

    /**
     * jsonp的返回
     * 
     * @param  array  $data 数据
     */
    public static function jsonp($data)
    {
        header('Content-type: text/javascript');

        $callback = urlencode($_GET['callback']);

        return "window[decodeURIComponent('{$callback}')](" . json_encode($data) . ")";
    }

    /**
     * 增加支持redirect的url参数，支持驼峰命名的 Action 名字
     *
     * @see yii\web\Controller::redirect()
     */
    public function redirect($url, $statusCode = 302)
    {
        if (is_array($url)) {
            $url[0] = Inflector::camel2id($url[0]);
        } else {
            $url = Inflector::camel2id($url);
        }

        return parent::redirect($url, $statusCode);
    }

    /**
     * 根据本次提交的token和上次提交的token判断，是否重复提交
     * 如果未设置 $token 且表单中不含有默认的_csrf参数，则不校验
     * 
     * @param string $token 唯一的随机字符串
     * @return boolean
     */
    protected function isRepeatSubmit($token = null)
    {
        $csrf = Yii::$app->request->post(Yii::$app->request->csrfParam);
        if ($token === null && !$csrf) {
            return false;
        } else {
            $token = $token ?: $csrf;
            $record = session(self::$formTokenSession);
            return $token === $record;
        }
    }

    /**
     * 记录本次提交的token
     * 如果未设置 $token 且表单中不含有默认的_csrf参数，则不记录
     * 
     * @param string $token 唯一的随机字符串
     */
    protected function recordSubmit($token = null)
    {
        $csrf = Yii::$app->request->post(Yii::$app->request->csrfParam);
        if ($token !== null || $csrf) {
            $token = $token ?: $csrf;
            session(self::$formTokenSession, $token);
        }
    }
}
