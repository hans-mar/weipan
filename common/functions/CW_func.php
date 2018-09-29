<?php

/**
 * 是否正在访问后台
 *
 * @param  string  $adminName 后台模块的路由名称
 * @return boolean
 */
function is_access_admin($adminName = 'admin')
{
    $host = explode('.', $_SERVER['HTTP_HOST'])[0];
    if ($host === $adminName) {
        return true;
    } elseif (($uri = $_SERVER['REQUEST_URI']) !== '/') {
        $params = preg_split('#[/\?]#', $uri);
        $id = $params[1];
        return $id === $adminName;
    } else {
        return false;
    }
}

/**
 * 过滤启动项配置
 * 
 * @param  array $bootstrap 能直接通过路由加载的模块
 * @param  array $extra     必须加载的模块
 * @return array            筛选过后需要加载的模块
 */
function bootstrap_filter($bootstrap = [], $extra = [])
{
    $isCli = PHP_SAPI === 'cli';
    if (!$isCli) {
        $host = explode('.', $_SERVER['HTTP_HOST'])[0];
        if (in_array($host, $bootstrap)) {
            $bootstrap = [$host];
        } elseif (($uri = $_SERVER['REQUEST_URI']) !== '/') {
            $params = preg_split('#[/\?]#', $uri);
            $id = $params[1];
            $bootstrap = array_filter($bootstrap, function ($value) use ($id) {
                return $value === $id;
            });
        } else {
            $bootstrap = [];
        }
    } else {
        $bootstrap = [];
    }
    if (!$isCli) {
        $bootstrap = array_unique(array_merge($bootstrap, $extra));
    }

    return $bootstrap;
}

/**
 * 将xml转为array
 * @param string $xml
 */
function fromXml($xml)
{ 
    if(!$xml){
      throw new Exception("xml数据异常！");
    }
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);    
}

// 文件记录方式记录log
function file_log($data)
{
    file_put_contents('file_log.txt', date('Y-m-d H:i:s') . ':' . var_export($data, true) . "\r\n", FILE_APPEND);
}

/**
 * 发送短信
 */
function sendsms($tel, $code)
{
    if (strlen(trim($tel)) != 11) {
      return ['code' => 1, 'info' => '您输入的不是一个手机号！'];
    }
    $ip = str_replace('.', '_', isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);

    if (session('ip_' . $ip)) {
      return ['code' => 1, 'info' => '短信已发送请在60秒后再次点击发送！'];
    }
    session('ip_' . $ip, $tel, 60);

    $post_data = array();
    $post_data['userid'] = 1717;
    $post_data['account'] = 'SXQL';
    $post_data['password'] = '123456';
    $post_data['content'] = '【'.config('web_sign', '夕秀软件').'】您的手机验证码为'. $code . '如非本人请忽略操作'; //短信内容需要用urlencode编码下
    $post_data['mobile'] =$tel;
    $post_data['sendtime'] = ''; //不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值
    $url='http://115.29.242.32:8888/sms.aspx?action=send';
    $o = '';
    foreach ($post_data as $k=>$v)
    {
        $o.= "$k=" . urlencode($v) . '&';
    }
    $post_data=substr($o, 0, -1);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
    $result = curl_exec($ch);

    $result = fromXml($result);
    if ($result['returnstatus'] == 'Success') {
      return ['code' => 2, 'info' => '发送成功！'];
    }
    return ['code' => 1, 'info' => '发送失败！'];
}

/**
 * 删除文件夹
 */
function deleteDir($dir) { 
    //先删除目录下的文件： 
    $dh = opendir($dir); 
    while($file = readdir($dh)) { 
        if($file!="." && $file!="..") { 
            $fullpath = $dir."/".$file; 
            if(!is_dir($fullpath)) { 
                unlink($fullpath); 
            } else { 
                deldir($fullpath); 
            } 
        } 
    } 
    closedir($dh); 
    //删除当前文件夹： 
    if(rmdir($dir)) { 
        return true; 
    } else { 
        return false; 
    } 
}

/**
 * 调试专用，可以传入任意多的变量进行打印查看
 */
function tes()
{
    $isCli = PHP_SAPI === 'cli';
    if (!$isCli && !in_array('Content-type:text/html;charset=utf-8', headers_list())) {
        header('Content-type:text/html;charset=utf-8');
    }

    if (in_array(debug_backtrace()[2]['function'], ['dump'])) {
        $printFunc = 'var_dump';
    } else {
        $printFunc = 'print_r';
    }

    foreach (func_get_args() as $msg) {
        if ($isCli) {
            $printFunc($msg);
            echo PHP_EOL;
        } else {
            echo '<xmp>';
            $printFunc($msg);
            echo '</xmp>';
        }
    }
}

/**
 * @see tes()
 */
function test()
{
    call_user_func_array('tes', func_get_args());
    exit;
}

/**
 * @see tes()
 */
function dump()
{
    call_user_func_array('tes', func_get_args());
    exit;
}

/**
 * 请求数据
 */
function httpRequest($url, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
