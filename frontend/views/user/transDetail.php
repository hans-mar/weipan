<?php common\components\View::regCss('jilu.css') ?>
<!-- <p class="charge-header" style="border:1px solid #ccc;"> 
<a href="javascript:window.history.back()" style="float: left;">
<img src="/images/arrow-left.png" style="width:40px;"></a>
<span>我的商品轨迹</span>
</p> -->
<div class="recording">
    <div class="rec_content" id="bean_list">
        <ul>
            <?= $this->render('_transDetail', compact('data')) ?>
        </ul>
    </div>
    <?php if ($pageCount < 2): ?>
        <div class="deta_more" id="deta_more_div">没有更多了</div>
    <?php else: ?>
        <div class="addMany" style="text-align: center;">
            <a style="" type="button" value="加载更多" id="loadMore" data-count="<?= $pageCount ?>" data-page="1">加载更多</a>
        </div>
    <?php endif ?>
</div>
<script type="text/javascript">
$(".addMany").on('click', '#loadMore', function() {
    var $this = $(this),
        page = parseInt($this.data('page')) + 1;

    $.get('', {p:page}, function(msg) {
        $("#bean_list>ul").append(msg.info);
        $this.data('page', page);
        if (page >= parseInt($this.data('count'))) {
            $('.addMany').hide();
        }
    });
});
</script>