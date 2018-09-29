<?php common\components\View::regCss('iconfont/iconfont.css') ?>
<?php common\components\View::regCss('login.css') ?>
<?php common\components\View::regCss('experience.css') ?>
<style type="text/css">body{background:#fff;}</style>

<!--头部导航-->
<div class="container">
    <div class="row pad_10 bor-bottom">
        <div class="col-xs-3">
            <a href="<?= url('user/index') ?>" class="back-icon"><i class="iconfont co_0">&#xe64e;</i></a>
        </div>
        <div class="col-xs-6 back-head co_0">我的体验卷</div>
        <div class="col-xs-3"></div>
    </div>
</div>
<!--中间内容-->
<div class="container mar_t10">
    <?php foreach ($userCoupons as $userCoupon) :?>
        <div class="row">
            <div class="price_btn">
                <div class="col-xs-4 mar">
                    <p class="mar_t10">￥<span class="font_28"><?= $userCoupon->coupon->amount ?></span></p>
                    <p>剩余<?= $userCoupon->number ?>张(<?= $userCoupon->coupon->product->name ?>)</p>
                </div>
                <div class="mar col-xs-8 font_12 text-right">
                    <p class="mar_t20">请尽快使用</p>
                    <p class="mar">过期天数</p>
                    <p><?= round((strtotime($userCoupon->valid_time) - time())/86400, 0) ?></p>
                </div>
            </div>
        </div>
    <?php endforeach ?>
    <div class="mar_t">
        <div class="reg_btn">
            <a href="<?= url('user/share') ?>">
                <button>分享赚体验卷</button>
            </a>
        </div>
    </div>
</div>
