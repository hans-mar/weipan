<div class="container">
    <div class="row">
    </div>
</div>

<?php
$appid = 'wxe46d9ad1b506e177';
$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri=http%3a%2f%2f' . $_SERVER['HTTP_HOST'] . '/site/shop&response_type=code&scope=snsapi_userinfo&state=index#wechat_redirect';
if(isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri=http%3a%2f%2f' . $_SERVER['HTTP_HOST'] . '/site/shop?uid='.$uid.'&response_type=code&scope=snsapi_userinfo&state=index#wechat_redirect';
}

?>
<script language="javascript">  
   setTimeout("window.location.href='<?= $url ?>';", 1);  
</script>
        