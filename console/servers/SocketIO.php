<?php

namespace console\servers;

use common\helpers\ArrayHelper;

/**
 * SocketIO 服务端
 * 
 * @author ChisWill
 */
class SocketIO extends \console\components\Server
{
    /**
     * @var array 访问用户的基本信息
     */
    protected $users = [];
    /**
     * @var integer 当前用户总数
     */
    protected $userCount = 0;
    /**
     * @var array 要绑定的事件列表
     */
    protected $events = [
        'disconnect',
        'login',
        'view',
        'chat'
    ];

    /**
     * 服务执行入口
     * 
     * @param  object $socket SocketIO 连接对象
     */
    public function run($socket)
    {
        $events = ArrayHelper::resetOptions($this->events, ['key' => 'event', 'value' => 'func']);
        foreach ($events as $params) {
            $event = $params['event'];
            $func = ArrayHelper::getValue($event, 'func', [$this, $event]);
            $socket->on($event, function ($data = null) use ($func, $socket) {
                try {
                    call_user_func($func, $socket, $data);
                } catch (\Exception $e) {
                    self::log($e, 'SocketIO/error');
                }
            });
        }
    }

    protected function disconnect($socket, $data)
    {
        if (isset($this->users[$socket->id])) {
            unset($this->users[$socket->id]);
            $this->userCount--;
            $socket->broadcast->emit('response', $socket->username . '离开了房间！（' . $this->userCount . '人）（' . date('H:i:s') . '）');
        }
    }

    protected function login($socket, $data)
    {
        $id = $data;
        // 找人
        $user = \common\models\AdminUser::findOne($id);
        if (!$user) {
            $socket->emit('response', '没有这个账号！');
        } elseif (!isset($this->users[$socket->id])) {
            // 登录
            $this->users[$socket->id] = $user->id;
            $socket->username = $user->realname;
            // 进入房间
            $socket->join($user->id);
            // 总登录数增加
            $this->userCount++;
            // 发送消息到浏览器
            $socket->emit('response', '你进入了房间。（' . $this->userCount . '人）（' . date('H:i:s') . '）');
            // 广播消息给其他人
            $socket->broadcast->emit('response', $user->realname . '进入了房间!（' . $this->userCount . '人）（' . date('H:i:s') . '）');
        } else {
            // 发送消息到浏览器
            $socket->emit('response', '你已经登录了！');
        }
    }

    protected function view($socket, $data)
    {
        if (!isset($this->users[$socket->id])) {
            $socket->emit('response', '请先登录！');
        } else {
            $socket->emit('response', $socket->username);
        }
    }

    protected function chat($socket, $data)
    {
        if (!isset($this->users[$socket->id])) {
            $socket->emit('response', '请先登录！');
        } else {
            $content = $data;
            $socket->emit('response', "我：" . $content . '（' . date('H:i:s') . '）');
            $socket->broadcast->emit('response', $socket->username . '：' . $content . '（' . date('H:i:s') . '）');
        }
    }
}
