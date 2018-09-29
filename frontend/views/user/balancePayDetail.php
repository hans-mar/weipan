<?php common\components\View::regCss('iconfont/iconfont.css') ?>
<?php common\components\View::regCss('login.css') ?>
<style type="text/css">body{background: #191919;font-size: 14px;}</style>
<div class="container">
    <div class="row pad_10">
        <div class="col-xs-3">
            <a href="<?= url('user/index') ?>" class="back-icon"><i class="iconfont">&#xe64e;</i></a>
        </div>
        <div class="col-xs-6 back-head">收支明细</div>
        <div class="col-xs-3"></div>
    </div>
    <div id="area">
        <?= $this->render('_balancePay', compact('data')) ?>
    </div>
    <?php if($pageCount > 1) : ?>
    <div class="row" style="text-align: center;">
        <a style="color: red;margin-top: 10px;" type="button" value="加载更多" id="loadMore" data-url="<?= url('user/ajaxBalancePay') ?>" data-count="<?= $pageCount ?>" data-page="1">加载更多</a>
    </div>
    <?php endif ?>
</div>
<div class="row text-center jymx_page">

</div>

<script type="text/javascript">
$(function() {
    $("#loadMore").click(function() {
        var $this = $(this),
            page = parseInt($this.data('page')) + 1;
        $.get('', {
            p: page
        }, function(html) {
            $("#area").append(html);
            $this.data('page', page);
            if (page >= parseInt($this.data('count'))) {
                $this.hide();
            }
        });
        return false;
    });
})
</script>