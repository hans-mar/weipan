<?php use common\helpers\Html; ?>
<?php use common\helpers\System; ?>
<!-- <p class="f-20 text-success">欢迎使用H-ui.admin <span class="f-14">v2.4</span>后台模版！</p> -->
<!-- <p>登录次数：18 </p> -->
<!-- <p>上次登录IP：222.35.131.79.1  上次登录时间：2014-6-14 11:19:55</p> -->
<!-- <table class="table table-border table-bordered table-bg">
    <thead>
        <tr>
            <th colspan="7" scope="col">信息统计</th>
        </tr>
        <tr class="text-c">
            <th>统计</th>
            <th>资讯库</th>
            <th>图片库</th>
            <th>产品库</th>
            <th>用户</th>
            <th>管理员</th>
        </tr>
    </thead>
    <tbody>
        <tr class="text-c">
            <td>总数</td>
            <td>92</td>
            <td>9</td>
            <td>0</td>
            <td>8</td>
            <td>20</td>
        </tr>
        <tr class="text-c">
            <td>今日</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
        </tr>
        <tr class="text-c">
            <td>昨日</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
        </tr>
        <tr class="text-c">
            <td>本周</td>
            <td>2</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
        </tr>
        <tr class="text-c">
            <td>本月</td>
            <td>2</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
        </tr>
    </tbody>
</table> -->
<table class="table table-border table-bordered table-bg">
    <thead>
        <tr>
            <th colspan="2" scope="col">服务器信息</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th width="30%">系统类型</th>
            <td><span id="lbServerName"><?= @php_uname('s') ?></span></td>
        </tr>
        <tr>
            <td>服务器操作系统</td>
            <td><?= @php_uname() ?></td>
        </tr>
        <tr>
            <td>PHP版本</td>
            <td><?= PHP_VERSION ?></td>
        </tr>
        <tr>
            <td>Mysql版本</td>
            <td><?= self::db('SELECT VERSION()')->queryScalar() ?></td>
        </tr>
        <tr>
            <td>PHP内存使用上限</td>
            <td><?= @ini_get('memory_limit') ?></td>
        </tr>
        <tr>
            <td>服务器脚本超时时间</td>
            <td><?= @ini_get('max_execution_time') ?>秒</td>
        </tr>
        <tr>
            <td>POST上传大小限制</td>
            <td><?= @ini_get('post_max_size') ?></td>
        </tr>
        <tr>
            <td>上传附件大小限制</td>
            <td><?= @ini_get('upload_max_filesize') ?: Html::errorSpan('不允许上传附件') ?></td>
        </tr>
        <tr>
            <td>服务器域名</td>
            <td><?= req()->getHostInfo() ?></td>
        </tr>
        <tr>
            <td>服务器端口</td>
            <td><?= $_SERVER['SERVER_PORT'] ?></td>
        </tr>
        <tr>
            <td>服务器的语言种类</td>
            <td><?= @ini_get('date.timezone') ?></td>
        </tr>
        <tr>
            <td>服务器当前时间</td>
            <td><?= self::$time ?></td>
        </tr>
        <?php if (!System::isWindowsOs() && u()->isMe()): ?>
        <tr>
            <td>服务器上次启动到现在已运行</td>
            <td><?= @explode(',', exec('uptime'))[0] ?></td>
        </tr>
        <?php endif ?>
        <tr>
            <td>系统所在文件夹</td>
            <td><?= Yii::$app->basePath ?></td>
        </tr>
        <tr>
            <td>PHP安装路径</td>
            <td><?= DEFAULT_INCLUDE_PATH ?></td>
        </tr>
        <tr>
            <td>当前系统用户名</td>
            <td><?= @get_current_user() ?></td>
        </tr>
        <tr>
            <td>浏览器信息</td>
            <td><?= req()->getUserAgent() ?></td>
        </tr>
        <tr>
            <td>当前SessionID</td>
            <td><?= session()->getId() ?></td>
        </tr>
    </tbody>
</table>