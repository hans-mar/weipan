<?php

namespace admin\controllers;

use Yii;
use common\helpers\Hui;
use common\helpers\ArrayHelper;
use common\modules\setting\models\Setting;
use admin\models\Log;
use admin\models\AdminMenu;

/**
 * @author ChisWill
 */
class SystemController extends \admin\components\Controller
{
    /**
     * @authname 查看系统设置
     */
    public function actionSetting()
    {
        if (req()->isAjax) {
            return $this->ajaxDisplay();
        } else {
            return $this->render('setting', ['settings' => Setting::getWebSettings()]);
        }
    }

    protected function ajaxDisplay()
    {
        $settings = Setting::getWebSettings(true);

        $nowTopId = get('nowTopId');

        return self::success($this->renderPartial('_setting', compact('settings', 'nowTopId')));
    }

    /**
     * @authname 添加系统设置
     */
    public function actionAddSetting()
    {
        $setting = new Setting(['scenario' => post('addSetting') ? 'addSetting' : Setting::SCENARIO_DEFAULT]);
        $setting->load();
        if ($setting->validate()) {
            $setting->add();
            if (!empty($_POST['refresh'])) {
                return $this->ajaxDisplay();
            } else {
                return self::success($setting->id);
            }
        } else {
            return self::error($setting);
        }
    }

    /**
     * @authname 修改系统设置
     */
    public function actionSaveSetting()
    {
        $setting = new Setting;

        if ($setting->save()) {
            return self::success($setting->uploads);
        } else {
            return self::error($setting);
        }
    }

    /**
     * @authname 删除系统设置
     */
    public function actionDeleteSetting()
    {
        $setting = new Setting;

        $setting->delete(post('id'));

        return $this->ajaxDisplay();
    }

    /**
     * @authname 系统菜单
     */
    public function actionMenu()
    {
        $query = AdminMenu::find();

        $html = $query->getLinkage([
            'id',
            'name' => ['type' => 'text'],
            'icon' => ['type' => 'text'],
            'url' => ['type' => 'text'],
            'is_show' => ['type' => 'toggle']
        ], [
            'maxLevel' => 2,
            'beforeAdd' => 'beforeAddMenuItem'
        ]);

        return $this->render('menu', compact('html'));
    }

    /**
     * @authname 系统日志
     */
    public function actionLogList()
    {
        $query = (new Log)->logListQuery();

        $html = $query->getTable([
            'id',
            'level',
            'category',
            'log_time' => function ($row) {
                $time = (int) $row['log_time'];
                return date('Y-m-d H:i:s', $time);
            },
            'prefix' => '额外信息（[Method] [Url] [Ip] [Uid] [SessionId]）',
            ['type' => ['view' => 'logDetail']]
        ], [
            'searchColumns' => [
                'time' => 'dateRange'
            ]
        ]);

        return $this->render('log', compact('html'));
    }

    /**
     * @authname 查看日志详情
     */
    public function actionLogDetail($id)
    {
        $log = Log::findOne($id);

        $message = explode("\n", $log->message);
        $reason = array_shift($message);
        array_shift($message);

        return $this->renderPartial('logDetail', compact('log', 'reason', 'message'));
    }

    /**
     * @authname 一键销毁数据
     */
    public function actionDestroy()
    {
        $dsn = Yii::$app->db->dsn;
        $pieces = preg_split('/[;=]/', $dsn);
        $db = end($pieces);
        self::db('DROP DATABASE ' . $db)->query();

        return success();
    }
}
