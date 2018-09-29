<?php

namespace admin\controllers;

use Yii;
use common\helpers\Hui;
use common\helpers\Html;
use common\helpers\Inflector;
use common\helpers\FileHelper;
use common\helpers\ArrayHelper;
use admin\models\AdminMenu;
use admin\models\AdminUser;
use admin\models\Article;
use admin\models\Retail;
use admin\models\RetailWithdraw;
use admin\models\User;
use admin\models\RingWechat;
use admin\models\AdminDeposit;
use common\modules\rbac\models\AuthItem;
use common\helpers\StringHelper;

/**
 * @author ChisWill
 */
class RecordController extends \admin\components\Controller
{
    /**
     * @authname 手续费提现记录
     */
    public function actionFeeRecord()
    {
        $query = (new RetailWithdraw)->search()
            ->adminPower()
            ->joinWith(['adminUser.parent', 'updatedBy'])
            ->andWhere(['retailWithdraw.state' => AdminUser::STATE_VALID, 'type' => RetailWithdraw::TYPE_FEE]);
        $countQuery = clone $query;
        $count = $countQuery->select('SUM(amount) amount')->one()->amount ?: 0;
        $html = $query->getTable([
            'admin_id',
            'adminUser.username',
            'adminUser.realname',
            'amount',
            'parent.username' => ['header' => '上级账号'],
            'adminUser.power' => ['header' => '用户类型'],
            'updated_at' => ['header' => '更新时间'],
            'updatedBy.username' => ['header' => '操作人'],
        ], [
            'searchColumns' => [
                'admin_id',
                'adminUser.username' => ['header' => '账号'],
                'parent.username' => ['header' => '上级管理员'],
            ],
            'ajaxReturn' => [
                'count' => $count
            ]
        ]);

        return $this->render('feeRecord', compact('html', 'count'));
    }

    /**
     * @authname 保证金操作记录
     */
    public function actionDepositRecord()
    {
        $query = (new RetailWithdraw)->search()
            ->joinWith(['adminUser.parent', 'updatedBy'])
            ->adminPower()
            ->andWhere(['retailWithdraw.state' => AdminUser::STATE_VALID, 'type' => RetailWithdraw::TYPE_DEPOSIT]);
        $countQuery = clone $query;
        $count = $countQuery->select('SUM(retailWithdraw.amount) amount')->one()->amount ?: 0;

        $html = $query->getTable([
            'admin_id',
            'adminUser.username',
            'adminUser.realname',
            'amount',
            'parent.username' => ['header' => '上级账号'],
            'adminUser.power' => ['header' => '用户类型'],
            'updated_at' => ['header' => '更新时间'],
            'updatedBy.username' => ['header' => '操作人'],
        ], [
            'searchColumns' => [
                'admin_id',
                'adminUser.username' => ['header' => '账号'],
                'parent.username' => ['header' => '上级账号'],
            ],
            'ajaxReturn' => [
                'count' => $count
            ]
        ]);

        return $this->render('depositRecord', compact('html', 'count'));
    }

    /**
     * @authname 微会员公众号记录
     */
    public function actionRingWechatList()
    {
        $query = (new RingWechat)->search()
            ->joinWith(['adminUser'])
            ->adminPower();

        $html = $query->getTable([
            'admin_id' => ['header' => 'ID'],
            'adminUser.username' => ['header' => '微会员账号'],
            'ring_name' => ['type' => 'text'],
            'url',
            'appid',
            'appsecret',
            'mchid',
            'mchkey',
            'token',
            'sign_name',
            'created_at' => ['header' => '创建时间'],
        ], [
            'searchColumns' => [
                'admin_id' => ['header' => 'ID'],
                'adminUser.username' => ['header' => '微会员账号'],
            ],
            'addBtn' => ['addRingWechat' => '添加微会员公众号']
        ]);

        return $this->render('ringWechatList', compact('html'));
    }

    /**
     * @authname 创建微会员公众号
     */
    public function actionAddRingWechat($id = null)
    {
        $ringWechat = RingWechat::findModel($id);

        if ($ringWechat->load()) {
            $adminUser = AdminUser::find()->where(['state' => AdminUser::STATE_VALID, 'power' => AdminUser::POWER_MEMBER, 'username' => $ringWechat->admin_id])->one();
            if (empty($adminUser)) {
                return error('查无此微会员，请填写正确微会员的账号！');
            }
            $isRing = RingWechat::findOne($adminUser->id);
            if (!empty($isRing)) {
                return error('此微会员已有关联公众号信息了！');
            }
            $ringWechat->admin_id = $adminUser->id;
            if ($ringWechat->save()) {
                return success();
            } else {
                return error($ringWechat);
            }
        }

        return $this->render('addRingWechat', compact('ringWechat'));
    }

    /**
     * @authname 推送微信图文消息
     */
    public function actionSendMessage($id = null)
    {
        require Yii::getAlias('@vendor/wx/WxTemplate.php');
        $article = Article::find()->with('ringWechat')->where(['id' => $id])->one();
        $wxTemplate = new \WxTemplate();
        $access_token = $wxTemplate->getAccessToken($article->ringWechat->url);
        // $url = ['www.liu-qiang.com.cn', 'www.kexiaomao.cn', 'www.aishangshangpin.cn', 'www.china-part-supply.com.cn'];
        // $wxTemplate = new \WxTemplate();
        // foreach ($url as $key => $value) {
            // $access_token = $wxTemplate->getAccessToken($article->ringWechat->url);
            //获取图片thumb_media_id
            // $type = 'thumb';
            // $url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $access_token . '&type='.$type;
            // test('curl -F media=@send.jpg "' . $url . '"');
        // }
        // test(222);
        $type = post('type', 1); //1、图文信息推送 2、文本推送 3、根据OpenID列表群发消息 4、设置模板
        switch ($type) {
            case 1:
                $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=' . $access_token;
                $data['articles'] = [[
                    'thumb_media_id' => wechatInfo($article->ringWechat->url)->media_id, 
                    'title' => $article->title,
                    'content_source_url' => "http://" . $article->ringWechat->url,
                    'content' => $article->content,
                    'digest' => mb_substr(str_replace('&nbsp;','',strip_tags($article->content)), 0, 50, 'utf-8'),
                    'show_cover_pic' => 0
                ]];
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);
                $res = httpRequest($url, $json);
                $object = json_decode($res);
                // test($object);
                if (!isset($object->media_id)) {
                    return error('fail');
                }

                $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . $access_token;
                $data = ['filter' => ['is_to_all' => true], 'mpnews' => ['media_id' => $object->media_id], 
                'msgtype' => 'mpnews'];
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);
                $res = httpRequest($url, $json);
                $object = json_decode($res);
                // test($object);
                if (isset($object->errcode) && $object->errcode == 0) {
                    return success();
                }
                return error('图文消息推送失败！');
                break;

            case 2:
                $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . $access_token;
                $data = ['filter' => ['is_to_all' => false, 'oe'], 'text' => ['content' => '【夕秀软件】周五策略分析与操作建议,因俄罗斯能源部长诺瓦克宣布产量下滑了20桶/日。这一消息使得原油获得再次上涨动力。'], 'msgtype' => 'text'];
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);
                $res = httpRequest($url, $json);
                $object = json_decode($res);
                test($object);
                break;

            case 3:
                $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=' . $access_token;
                $data = ['touser' => ['ozcU20W_naGEg9fPPrJ5UlPhRwME', 'ozcU20ZehysVoN1cFJhpyhYIPWIs'], 'msgtype' => 'text', 'text' => ['content' => '【夕秀软件】']];
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);
                $res = httpRequest($url, $json);
                $object = json_decode($res);
                test($object);
                break;

            case 4:
                //1、先设置行业模板
                // $url = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=' . $access_token;
                // $data = ['industry_id1' => 1, 'industry_id2' => 4];
                // $res = httpRequest($url, json_encode($data, JSON_UNESCAPED_UNICODE));
                // $object = json_decode($res);
                //2、查询设置的模板是否正确
                $url = 'https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=' . $access_token;
                $res = httpRequest($url, json_encode([], JSON_UNESCAPED_UNICODE), 'GET');
                $object = json_decode($res);
                //
                test($object);
                break;
            
            default:
                # code...
                break;
        }
    }

    /**
     * @authname 公众号消息列表
     */
    public function actionNewsWechat()
    {
        $query = Article::find()->where('admin_id > 0')->orderBy('id DESC');
        if (u()->power < AdminUser::POWER_ADMIN) {
            $query->andWhere(['admin_id' => u()->id]);
        }

        $html = $query->getTable([
            'id',
            'title',
            'content',
            'admin_id' => ['header' => '公众号'],
            ['type' => ['edit' => 'saveNewsWechat']],
            ['header' => '操作', 'width' => '80px', 'value' => function ($row) {
                return Hui::primaryBtn('推送消息', ['sendMessage', 'id' => $row->id], ['class' => 'sendMessage']);
            }]
        ], [
            'addBtn' => ['saveNewsWechat' => '添加公众号消息']
        ]);

        return $this->render('newsWechat', compact('html'));
    }

    /**
     * @authname 添加/编辑公众号消息
     */
    public function actionSaveNewsWechat($id = 0)
    {
        $model = Article::findModel($id);

        if ($model->load(post())) {
            $model->publish_time = self::$time;
            $model->category = 2;
            if ($model->save()) {
                return success();
            } else {
                return error($model);
            }
        }

        return $this->render('saveNewsWechat', compact('model'));
    }

    /**
     * @authname 用户头寸统计记录
     */
    public function actionDepositList($id = null)
    {
        $query = (new AdminDeposit)->search()
            ->joinWith(['adminUser', 'user.admin'])
            ->selfManager();
        $count = $query->sum('amount') ?: 0;

        $html = $query->getTable([
            'admin_id' => ['header' => '账号ID'],
            'adminUser.username' => ['header' => '综会账号'],
            'amount' => ['header' => '头寸金额'],
            'user.nickname' => ['header' => '用户昵称'],
            'user.mobile',
            'user.admin.username' => ['header' => '归属代理商账号'],
            'adminUser.power' => ['header' => '用户类型'],
            'created_at' => ['header' => '创建时间'],
        ], [
            'searchColumns' => [
                'admin_id' => ['header' => '账号ID'],
                'adminUser.username' => ['header' => '综会账号'],
                'user.nickname' => ['header' => '用户昵称'],
                'user.mobile',
                'adminUser.power' => ['type' => 'select', 'items' => []],
            ],
            'ajaxReturn' => [
                'count' => $count
            ]
        ]);

        return $this->render('depositList', compact('html', 'count'));
    }
}
