<style type="text/css">

    /*底部导航 -- start*/
    .footer-nav{
        position: fixed;
        width: 100%;
        bottom: 0;
        left: 0;
        background: #fff;
        border-top: 1px solid #eee;
    }
    .footer-nav>li{
        float: left;
        width:calc(100% / 3);
        height: 50px;
    }
    .footer-nav>li a{
        display: inline-block;
        width: 100%;
        height: 100%;
        text-align: center;
        font-size: 12px;
        color: #828284;
        line-height: 76px;
        background: url(../images/home.png) no-repeat center 5px;
        background-size: 25px 25px;
    }
    .footer-nav>li:nth-child(1) a{
        background: url(../images/home.png) no-repeat center 5px;
        background-size: 25px 25px;
    }

    .footer-nav>li:nth-child(1).active a{
        background: url(../images/home_2.png) no-repeat center 5px;
        background-size: 25px 25px;
        color: #1E9DFF;
    }

    .footer-nav>li:nth-child(2) a{
        background: url(../images/hongjiu.png) no-repeat center 5px;
        background-size: 25px 25px;
    }

    .footer-nav>li:nth-child(2).active a{
        background: url(../images/hongjiu_2.png) no-repeat center 5px;
        background-size: 25px 25px;
        color: #1E9DFF;
    }

    .footer-nav>li:nth-child(3) a{
        background: url(../images/jiaoyi.png) no-repeat center 5px;
        background-size: 25px 25px;
    }

    .footer-nav>li:nth-child(3).active a{
        background: url(../images/jiaoyi_2.png) no-repeat center 5px;
        background-size: 25px 25px;
        color: #1E9DFF;
    }


    .footer-nav>li:nth-child(4) a{
        background: url(../images/me.png) no-repeat center 5px;
        background-size: 25px 25px;
    }

    .footer-nav>li:nth-child(4).active a{
        background: url(../images/me_2.png) no-repeat center 5px;
        background-size: 25px 25px;
        color: #1E9DFF;
    }
    /*底部导航  -- end  */
    .shop-detail-container{
        margin-bottom: 90px;
        text-align: center;
    }


    .img-content img{
        width: 21.06%;
    }
    .img-content {
        margin: 35px 0;
    }
    .goods-name{
        font-size: 15px;
        color:#474747;
        text-align: left;
        padding-left: 16px;
    }
    .goods-price{
        color: #F74B4B;
        font-size: 15px;
        text-align: left;
        padding-left: 16px;
        margin: 15px 0; 
    }
    .yunfei{
        padding-left: 16px;
        font-size: 13px;
        color: #828284;
        text-align: left;
        border-top:1px dashed #DEDEDE;
        padding-top: 15px;
    }
    .detail-title{
        font-size: 14px;
        color: #474747;
        margin-top: 40px;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }
    .detail-title:after,.detail-title:before{
        content: "";
        display: inline-block;
        width: 15%;
        border-top:1px solid #474747;
        vertical-align: middle;
        margin: 0 10px;
    }
    .detail-img{
        width: 100%;
    }
    .bottom-btn-group{
        position: fixed;
        bottom: 0;
        right: 0;
        z-index: 1000000000;
        width: 75%;
        height: 50px;
        background: #61BAFF;
    }
    .bottom-btn-group a{
        float: left;
        width: 50%;
        height: 100%;
        line-height: 50px;
        text-align: center;
        font-size: 14px;
        color: #fff;
    }
    .bottom-btn-group a.active{
        background: #1E9DFF;
    }
</style>

<div class="shop-detail-container">
    <div class="img-content">
        <img src="../images/boer-lafei.png">
    </div>
    <p class="goods-name">博尔特拉菲</p>
    <p class="goods-price">￥450</p>
    <p class="yunfei">快递费：￥5元</p>
    <p class="detail-title">酒详情页</p>
    <img class="detail-img" src="../images/boer-lafei-img.jpg">
</div>
<p class="bottom-btn-group clear-fl">
    <a href="<?= url(['site/index']) ?>">免费领货</a>
    <a class="active" href="<?= url(['site/index']) ?>">立即购买</a>
</p>

<ul class="clear-fl   footer-nav">
    <li class="active"><a href="<?= url('site/shop') ?>">
        商城
    </a></li>
    <li><a href="<?= url('site/index') ?>">
        交易
    </a></li>
    <li><a href="<?= url('user/index') ?>">
        我的
    </a></li>
</ul>

<script type="text/javascript">
    $(".footer-nav").on("click","li",function(){
        $(this).addClass("active").siblings(".active").removeClass("active");
    });
</script>