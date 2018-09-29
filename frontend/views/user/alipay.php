<style type="text/css">
    .main{
        background:white;
        width: calc(100% - 40px);
        position: relative;
        height:98vh;
        margin: 0 auto;
    }
    .top-tip{
        font-size:18px;
        color:black;
        height:70px;
        text-align: center;
        position: relative;
        top: 40px;
        /*background:#fff url(/images/alipay.png)no-repeat 85px center;*/
        background:#fff;
        background-size: 40px 40px;
        font-weight: 800;
        border-bottom: 1px dashed #000;
    }
    .welcome{
        width:250px;
        margin:0 auto;
        height:34px;
        line-height: 34px;
        text-align: center;
        font-size:20px;
        color:#00aaef;
        background: #fff;
        margin-top:30px;
        border-radius:18px;
    }
    .img-content{
        width:225px;
        margin:0 auto;
        height:225px;
        margin-top:25px;
    }
    .img-content img{
        width:100%;
        margin-top:15%;
    }
    .notice{
        margin-top:15px;
        font-weight: 500;
        font-size: 14px;
        color:red;
        padding-left: 10px;
        padding-right: 10px;
    }
    .notice>span{
        border-bottom:1px dashed #FDFEFF;
        color:#fff;
        font-size:24px;
        padding-bottom:10px;
    }
    .back-btn{
        display: block;
        width: 150px;
        height: 40px;
        border-radius:20px;
        margin:0 auto;
        background: #fff;
        text-align: center;
        line-height: 40px;
        font-size:22px;
        color:#00aaef;
        margin-top:20px;
    }
</style>
    <body style="background-color: orange">
        <div class="main">
     <div class="top-tip">
         请在支付宝中完成支付！
         <!-- 长按二维码，在右上角选择浏览器打开完成支付！ -->
     </div>
     
    <div class="main-content" style="background-color: white">
        <div class="img-content">
            <img src="<?= $html ?>">
        </div>
        <div class="notice">
            <span>支付</span>   
        </div>
        <div class="notice">
        具体流程：<br>
        <!-- 长按二维码，跳出页面后长按复制链接，再退出此页面，打开手机UC浏览器，把复制的链接粘贴到浏览器完成支付 -->
        1、请将该页面截图，然后打开支付宝app<br>
        2、进入首页“扫一扫”，选择相册，扫描该张截图<br>
        3、输入密码完成支付动作<br>
        4、回到个人中心，查看资金账户余额  
<!--         1、长按二维码，跳出页面后点击右上角"…"<br>
        2、选择"在浏览器中打，<br>
        3、跳出"网页请求打开支付宝"<br>
        4、点击"打开"，输入密码完成支付 -->
        </div>
    </div>
 </div>
    </body>
 