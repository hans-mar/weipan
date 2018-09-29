<?php common\components\View::regCss('iconfont/iconfont.css') ?>
<?php common\components\View::regCss('mine.css') ?>
<style type="text/css">
/*body {background:#fff;}*/
.head {
    background-color:#e6262e;
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
</style>
<!--头部导航-->
<div class="container" style="background-color:#C5030B;">
    <div class="row ">
        <div class="col-xs-12">
            <div class="media">
                <div class="media-left media-middle">
                    <p class=" mine-pic">
                        <img src="<?= u()->face ?>" width="73" height="73">
                    </p>
                </div>
                <div class="media-body">
                    <!--  <p>经纪人姓名:李丽丽</p> -->
                    <p>下线客户:<?= count($idArr[0]) + count($idArr[1]) ?>人</p>
                    <p>商品总额:<?= array_sum($data['amount']) ?>￥</p>
                    <p>返点总额:<?= array_sum($data['rebate']) ?>￥</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!--中间内容-->
<div class="container bg_h">
    <div class="row head">
        <div class="col-xs-4 title lineright"><span>费用明细</span></div>
        <div class="col-xs-4 title lineright"><span>商品总额</span></div>
        <div class="col-xs-4 title"><span>返点总额</span></div>
    </div>
    <div class="row content">
        <div class="col-xs-4 list"><span>推广费</span></div>
        <div class="col-xs-4 list"><span><?= $data['amount'][0] ?>￥</span></div>
        <div class="col-xs-4 list"><span><?= $data['rebate'][0] ?>￥</span></div>
    </div>

    <div class="row content">
        <div class="col-xs-4 list"><span>服务费</span></div>
        <div class="col-xs-4 list"><span><?= $data['amount'][1] ?>￥</span></div>
        <div class="col-xs-4 list"><span><?= $data['rebate'][1] ?>￥</span></div>
    </div>

    <div class="row">
         <div class="col-xs-12 list"><a href="<?= url('user/myOffline') ?>"  class="checkbutton">查看我的下级</a></div>
    </div>
   
</div>

<div class="row"></div>
<!--底部导航 begin-->
<div class="nav navbar-fixed-bottom clearfix">
    <ul class="footer_nav" style="margin-bottom:-10px;">
        <li>
            <a href="<?= url('site/index') ?>" class="img-foot">
                <img src="/images/mine_1.png" width="20" height="22" class="img1">
                <img src="/images/index_1.png" width="20" height="22" class="img2" style="display:none;">
                <p>分析</p>
            </a>
        </li>
        <li>
            <a href="<?= url('order/position') ?>" class="img-foot">
                <img src="/images/mine_2.png" width="18" height="22" class="img1">
                <img src="/images/cqd_4.png" width="18" height="22" class="img2" style="display:none;">
                <p>持仓单</p>
            </a>
        </li>
        <li>
            <a href="<?= url('user/share') ?>" class="img-foot">
                <img src="/images/mine_3.png" width="22" height="22" class="img1">
                <img src="/images/mian_15.png" width="22" height="22" class="img2" style="display:none;">
                <p>邀请</p>
            </a>
        </li>
        <li>
            <a class="img-foot loginBtn" data-user="<?= user()->isGuest ?>">
                <img src="/images/cqd_5.png" width="19" height="22" class="img1" style="display:none;">
                <img src="/images/main_11.png" width="19" height="22" class="img2">
                <p style="color:#e6262e;">个人中心</p>
            </a>
        </li>
    </ul>
</div>

<script>
$(function () {
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    $.alert(msg.info);
                } else {
                    $.alert(msg.info);
                    window.location.href = '<?= url('user/index') ?>'
                }
            }
        }));
        return false;
    });
});
</script>