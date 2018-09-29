<?php common\components\View::regCss('iconfont/iconfont.css') ?>
<?php common\components\View::regCss('mine.css') ?>
<?php common\components\View::regCss('page.css') ?>
<?php common\components\View::regCss('bootstrap.min.css') ?>
<style type="text/css">
/*body {background:#fff;}*/
.head {
    background-color:#ef6369;
    text-align: center;
    box-shadow: 5px 5px 3px #BD5252;
}
.title {
    color: #fff;
    display: block;
    font-size: 15px;
    padding: 10px 0;    
    font-weight: 500px;
    font-family: '微软雅黑';
}
.lineright{border-right:1px solid #fff;}
.list {
    color: #333;
    margin-top: 10px;
    text-align: center;
}
.bg_h {
    background-color: #fff;
    height: 400px;
    color:#333;
}
.content {
    padding: 10px 0;
    margin: 0 6px;
    border-bottom: 1px solid red;
}
.checkbutton{
        background: #f00;
    width: 70%;
    margin: 10px auto;
    color: #fff;
    padding: 10px 0;
    border-radius: 25px;
    font-size: 16px;
}
.banner{
    margin-bottom: 15px;
    height: 50px;
}
.tit{
    height: 100%;
    color: #fff;
    font-size: 15px;
    line-height: 50px;
}
.tit a{
    color: #fff;

}

</style>
<!--头部导航-->
<div class="container bg_h">
    <div class="row banner" style="background-color: #094078;">
        <div class="col-xs-3 tit"><a href="/user/share"><i class="iconfont"></i></a></div>
        <div class="col-xs-6 tit">下线列表</div>
        <div class="col-xs-3 tit"></div>
    </div>
    <div class="row head trace">

        <div class="col-xs-4 title lineright"><span>手机号</span></div>
        <div class="col-xs-4 title lineright"><span>充值总额</span></div>
        <div class="col-xs-4 title"><span>返点总额</span></div>
    </div>
    <?= $this->render('_myOffline', compact('data')) ?>
    <?= self::linkPager() ?>


</div>