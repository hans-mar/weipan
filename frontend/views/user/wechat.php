<style type="text/css">
    .pay-title{
        font-size:41px;
        color:#81b826;
        text-align: center;
        margin-bottom:10px;
        margin-top:40px;
    }
    .pay-title img{
        width:55px;
        vertical-align: middle;
        margin-right:8px;
        position: relative;
        top:-3px;
    }
    .welcome{
        width:240px;
        margin:0 auto;
        border:1px solid #81B826;
        border-radius:15px;
        height:32px;
        line-height:32px;
        text-align: center;
        color:#81B827;
        font-size:20px;
    }
    .img-content{
        display: block;
        background:url(/images/ewm.png) no-repeat center center;
        background-size:163px;
        width:160px;
        height:160px;
        padding:15px;
        margin:0 auto;
        margin-top:35px;
    }
    .img-content img{
        width:100%;
        height:100%;
    }
    .notice{
        font-size:23px;
        color:#565656;
        width:270px;
        margin:0 auto;
        border-bottom:1px dashed #434343;
        margin-top:10px;
        padding-bottom:10px;
    }
    .bold{
        font-size:30px;
    }
    .acount{
        display: inline-block;
        width:60px;
        color:#F54A4A;
        text-align: center;
    }
    .back-btn{
        display: block;
        width:150px;
        height: 30px;
        border-radius:15px;
        background: #F64A4A;
        line-height: 30px;
        text-align: center;
        color:#fff;
        font-size:20px;
        text-decoration: none;
        margin:0 auto;
        margin-top:20px;
    }
</style>
<div class="order">
    <h1 class="pay-title"><img src="/images/ic.png">微信支付</h1>
    <p class="welcome">欢迎使用微信支付</p>
    <div  class="img-content">
        <img src="<?= $html ?>">
    </div>
    <div class="notice">
    具体流程：<br>
    请勿长按识别二维码支付，请截图二维码保存到手机，打开微信扫一扫，选择相册中对应二维码扫描即可完成支付
    </div>
</div>