<?= $this->render('_head') ?><!--引入公共头部-->
  <body>
 
    <!--    首页      -->
    <!--固定头部-->
            <ul class="index-head flex col-w">
                    <li class="le" style="width:1.2rem"></li>
                    <li class="mid"><?=config('web_name')?></li>
                    <li class="ri" style="width:1.2rem">
                                <a class="col-w  f-ri" style="padding-right:.1rem" href="/user"><?=u()->nickname ?></a>
                    </li>
            </ul>
            <div style="height: .45rem; width: 100%;"></div>
            
            <!--        滚动图片        -->
        <div class="swiper-container swiper-container-horizontal">
            <div class="swiper-wrapper" style="transform: translate3d(-5709px, 0px, 0px); transition-duration: 0ms;">
                        <div class="swiper-slide" style="width: 1903px;">
                            <a href="#"><img src="/test/20170421103129278.jpg" width="100%"></a>
                        </div>
                        <div class="swiper-slide" style="width: 1903px;">
                            <a href="#"><img src="/test/20170331165156760.jpg" width="100%"></a>
                        </div>
                        <div class="swiper-slide swiper-slide-prev" style="width: 1903px;">
                            <a href="#"><img src="/test/20170421103200761.jpg" width="100%"></a>
                        </div>
                        <div class="swiper-slide swiper-slide-active" style="width: 1903px;">
                            <a href="#"><img src="/test/20170421103217491.jpg" width="100%"></a>
                        </div>
                        <div class="swiper-slide swiper-slide-next" style="width: 1903px;">
                            <a href="#"><img src="/test/2017042110323524.jpg" width="100%"></a>
                        </div>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets"><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span><span class="swiper-pagination-bullet"></span></div>
        </div>
        
        <!--        头部菜单                   -->
        <ul class="index-tab flex">
		<!--/site/detail?type=sim-->
            <li>
                <a href="/site/detail?type=sim"><i class="iconfont col-4"></i>
                    <p class="col-1">模拟</p>
                </a>
            </li>
            <li>
                <a href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?=config('qq')?>&amp;site=qq&amp;menu=yes" target="_blank"><i class="iconfont col-4"></i>
                    <p class="col-1">咨询</p>
                </a>
            </li>
            <li>
                <a href="/site/wx">

                    <img src="/test/app.png" alt="" style="margin-top: 6px;width: 35px;height: auto;">
                    <p class="col-1" style="">APP下载</p>
                </a>
            </li>
        </ul>
        <div style="height: .1rem"></div>
        <!--            首页热门        -->
        <div class="index-hot">
            <h3 class="col-2  flex2"><span>热门交易</span><span class="fr flex2"><em>最新价</em><em>涨幅</em></span></h3>
            <!-- 商品列表 -->
            <ul id="pro">
                <?php foreach ($productArr as $key => $value): ?>
                        <li data-pro-no="<?= $value['table_name'] ?>">
                            <a class="flex2" href="/site/detail?pid=<?=$value['id'] ?>">
                            
                            <?php $class='active';if ($value['price'] > $value['close']){ $class = '';}?>
                            <em><p class="col-1"><?= $value['name'] ?></p><p class="col-3" style="font-size:.12rem"><?= $value['table_name'] ?></p></em>
                            <span class="ri flex2 <?=$class ?>">
                                <p><?=number_format($value['price'],0,".","") ?></p>
                                <p><?=$value['diff_rate'] ?>%</p>
                            </span>
                            </a>
                        </li>
                <?php endforeach ?>
                        
            </ul>
        </div>
        
        <!--                底部按钮                    -->
        <div class="index-bot-btns">
            <div>
                <p>交易由纽约商品交易所，香港交易所，新加坡交易所等提供实盘对接</p>
                
            </div>
            <p>投资有风险，入市须谨慎</p>
            <p>客服：<?=config('tel')?></p>
        </div>
        <?= $this->render('_foot') ?><!--引入公共底部-->

        
<script src="/test/swiper.min.js"></script>
<script src="/test/jquery.cookie.js"></script>
<script>
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                autoplay: 2800,
                autoplayDisableOnInteraction: false,
            });


            $("#sim").click(function(){
                layer.open({
                className:'index-msg',
                content: "暂未开放",
                btn: ['确定']
            })
            })
    
    $(function(){
        var proNos = '';
        var msg = '';
        if(msg != ''){
            layer.open({
                className:'index-msg',
                content: msg,
                btn: ['确定']
            })
        }
        $('#pro li').each(function(i){
            proNos += (i == 0 ? '' : ',') + $(this).data('proNo')
        })
        $.ajax({
            url: '<?= url('site/proCloseList')?>',//ProCloseList
            data: {
                proNo: proNos
            },
            success: function(data){
                var obj = data.data;
                for(var prop in obj){
                    $('[data-pro-no=' + prop + ']').data('preClose', obj[prop]);
                }
                setInterval(queryIndices, 1000);
                queryIndices();
                
            }
        })
        
        
        function queryIndices(){
            $.ajax({
                url: '<?= url('site/proPriceList')?>',
                data: {
                    proNo: proNos
                },
                success: function(data){
                    var obj = data.data;
                    for(var prop in obj){
                        var indices = obj[prop];
                        var preClose = $('[data-pro-no=' + prop + ']').data('preClose');
                        var p = $('[data-pro-no=' + prop + '] span p')
                        if(preClose != undefined && preClose != null && preClose != 0 && indices != null){
                            
                            if(indices < preClose){
                                p.parent().addClass('active');
                            }

                            p.eq(0).text(parseFloat(indices));
                            ((indices / preClose - 1) * 100).toFixed(2) > 0 ? p.eq(1).text("+"+((indices / preClose - 1) * 100).toFixed(2) + '%') : p.eq(1).text(((indices / preClose - 1) * 100).toFixed(2) + '%')
                            
                            
                        }else{
                            p.parent().addClass('active3');
                            p.eq(0).text("- -.- -");
                            p.eq(1).text("停市");
                        }
                    }
                }
            })
        }
    })

    
    function getQueryString(name)
    {
         var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
         var r = window.location.search.substr(1).match(reg);
         if(r!=null)return  unescape(r[2]); return null;
    }
    var rid = getQueryString("rid");
    var COOKIE_NAME = 'yht_rid';
    if(rid != null && rid != ''){
        $.cookie(COOKIE_NAME, rid, {path:'/', expires:3});
    }
        </script>
  
</body></html>