        <div class="nav navbar-fixed-bottom clearfix">
            <ul class="footer_nav" style="margin-bottom:-10px;">
                <li>
                    <a href="<?= url('site/index') ?>" class="img-foot">
                        <img src="/images/main_16.png" width="18" height="22" class="img1" style="display:none;">
                        <img src="/images/index_1.png" width="18" height="22" class="img2">
                        <em class="font_12" style="color:#e6262e;">分析</em>
                    </a>
                </li>
                <li>
                    <a href="<?= url('order/position') ?>" class="img-foot">
                        <img src="/images/index_7.png" width="18" height="22" class="img1">
                        <img src="/images/cqd_4.png" width="18" height="22" class="img2" style="display:none;">
                        <em class="font_12">持仓单</em>
                    </a>
                </li>
                <li>
                    <a href="<?= url('user/share') ?>" class="img-foot">
                        <img src="/images/cqd_6.png" width="18" height="22" class="img1">
                        <img src="/images/mian_15.png" width="18" height="22" class="img2" style="display:none;">
                        <em class="font_12">邀请</em>
                    </a>
                </li>
                <li>
                    <a class="img-foot loginBtn" data-user="<?= user()->isGuest ?>">
                        <img src="/images/cqd_5.png" width="18" height="22" class="img1">
                        <img src="/images/main_11.png" width="18" height="22" class="img2" style="display:none;">
                        <em class="font_12">个人中心</em>
                    </a>
                </li>
            </ul>
        </div>