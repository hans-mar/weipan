<style type="text/css">
    body{
        width: 100vw;
        overflow-x: hidden;
    }
    .shop-banner img{
        width: 100%;
    }
    .shop-banner{
        margin-bottom: 12px;
    }
    .shop-img-content{
        padding: 0 10px;
    }
    .shop-img-content img{
        float: left;
        width: calc((100% - 20px) / 3);
    }
    .shop-img-content img+img{
        margin-left: 10px;
    }
    .clear-fl:after{
        content: "";
        display: block;
        clear: both;
    }
    .nice-goods{
        font-size: 14px;
        color: #474747;
        text-align: center;
        margin: 15px 0;
    }
    .nice-goods:after,.nice-goods:before{
        content:"";
        display: inline-block;
        width: 6px;
        height: 6px;
        background: #474747;
        transform:rotate(45deg);
        margin: 0 8px;
        vertical-align: middle;
    }
    .goods-list{
        border-top:1px solid #F3F3F3;
    }
    .goods-list li{
        float:left;
        width: 50%;
        border-bottom: 1px solid #F3F3F3;
        border-right: 1px solid #F3F3F3;
        box-sizing:border-box;
    }
    .goods-list li:nth-child(2n){
        border-right: 0;
    }
    .goods-list li a{
        width: 100%;
        text-align: center;
        padding: 8px;
    }
    .goods-list li img{
        width: 19.2%;
    }
    .goods-name{
        font-size: 13px;
        color: #474747;
    }
    .goods-price{
        font-size: 13px;
        color: #F74B4B;
    }

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
    .shop-container{
        margin-bottom: 60px;
    }
</style>

<div class="shop-container">
    <div class="shop-banner">
        <img src="../images/banner.png">
    </div>
    <div class="shop-img-content clear-fl">
        <img src="../images/a1.png">
        <img src="../images/a2.png">
        <img src="../images/a3.png">
    </div>
    <p class="nice-goods">精选商品</p>
    <ul class="goods-list clear-fl">
        <li>
            <a href="<?= url('site/one') ?>">
                <img src="../images/aozhou.png">
                <p class="goods-name">澳洲拉菲</p>
                <p class="goods-price">￥500</p>
            </a>
        </li>
        <li>
            <a href="<?= url('site/two') ?>">
                <img src="../images/bosaier.png">
                <p class="goods-name">波尔图拉菲</p>
                <p class="goods-price">￥450</p>
            </a>
        </li>
        <li>
            <a href="<?= url('site/three') ?>">
                <img src="../images/xibanya.png">
                <p class="goods-name">西班牙拉菲</p>
                <p class="goods-price">￥400</p>
            </a>
        </li>
    </ul>
</div>

<script type="text/javascript">
    $(".footer-nav").on("click","li",function(){
        $(this).addClass("active").siblings(".active").removeClass("active");
    });
</script>