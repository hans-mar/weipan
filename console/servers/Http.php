<?php

namespace console\servers;

use Yii;

/**
 * Http 服务端
 * 
 * @author ChisWill
 */
class Http extends \console\components\Server
{
    /**
     * @var object SocketIO 连接对象
     */
    public $io;

    /**
     * 服务执行入口
     * 
     * @param  object $http 请求连接对象
     * @param  mixed  $data 请求参数
     * @return mixed        返回结果
     */
    public function run($http, $data)
    {
        $io = $this->io;
        $req = $_POST ?: $_GET;
        if (!isset($req['event'])) {
            return $http->send('fail');
        } else {
            try {
                return $http->send(call_user_func([$this, $req['event']], $io, $data));
            } catch (\Exception $e) {
                self::log($e, 'HttpPush/error');
                return $http->send('fail');
            }
        }
    }

    protected function demo($io, $data)
    {
        if ($to = $data['post']['to']) {
            $io->to($to)->emit('response', $data['post']['content']);
        } else {
            $io->emit('response', $data['post']['content']);
        }
        return 'ok';
    }
}
