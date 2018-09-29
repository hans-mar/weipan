<?php use common\helpers\Html;
      use frontend\models\Product; ?>
<?php $this->regCss('iconfont/iconfont.css') ?>
<?php $this->regCss('list.css') ?>
<?php $this->regCss('manager.css') ?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<div id="main">
    <div class="twoDCode-box">
        <div class="codeImg">
            <img src="<?= $img ?>" id="codeImg" />
        </div>
        <div class="codeTxtDiv">
            <p class="codeTxt" id="codeImgTxt">扫扫二维码，马上体验微盘赚钱</p>
        </div>
    </div>
</div>
<script type="text/javascript">
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '<?= WX_APPID ?>', // 必填，公众号的唯一标识
        timestamp: '<?= $wxConfig['timestamp'] ?>', // 必填，生成签名的时间戳
        nonceStr: '<?= $wxConfig['noncestr'] ?>', // 必填，生成签名的随机串
        signature: '<?= $wxConfig['signature'] ?>',// 必填，签名，见附录1
        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });

    //成功
    wx.ready(function(){
        var data = {};
        data.title = '<?= $user->nickname ?>的名片',  // 分享标题
        data.link = 'http://<?= $_SERVER['HTTP_HOST'] . url(['manager/myCode', 'id' => get('id')]) ?>',  // 分享链接
        data.desc = '二维码图片 扫描二维码，加入微盘，马上赚钱',  //分享描述
        data.imgUrl = 'http://<?= $_SERVER['HTTP_HOST'] . config('web_logo') ?>'; // 分享图标
        //朋友圈
        wx.onMenuShareTimeline({
            title: data.title, // 分享标题
            link: data.link, // 分享链接
            imgUrl: data.imgUrl, // 分享图标
            success: function () { },
            cancel: function () { }
        });
        //发送给好友
        wx.onMenuShareAppMessage({
            title: data.title, // 分享标题
            desc: data.desc, // 分享描述
            link: data.link, // 分享链接
            imgUrl: data.imgUrl, // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {},
            cancel: function () {}
        });
        //QQ好友
        wx.onMenuShareQQ({
            title: data.title, // 分享标题
            desc: data.desc, // 分享描述
            link: data.link, // 分享链接
            imgUrl: data.imgUrl, // 分享图标
            success: function () { },
            cancel: function () { }
        });
        //腾讯微博
        wx.onMenuShareWeibo({
            title: data.title, // 分享标题
            desc: data.desc, // 分享描述
            link: data.link, // 分享链接
            imgUrl: data.imgUrl, // 分享图标
            success: function () { },
            cancel: function () { }
        });
        // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
    });
    //失败
    wx.error(function(res){

        // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。

    });
</script>

