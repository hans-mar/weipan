<?= $this->render('_head') ?><!--引入公共头部-->

<body style="">
        <div class="tr-box flex-c2">     
        <!--    首页      -->
            <ul class="index-head flex col-w <?=$type?>">
                    <li class="le"><a href="/site/index" class="col-w"><i class="iconfont"></i></a></li>
                    <li class="mid" data-product-id="<?=$product->id?>" data-pro-no="<?=$product->table_name?>"><?=session('sim_type')=='sim'?'模拟':'实盘'?>交易-<?=$product->name ?><i class="iconfont"></i></li>
                    <li class="ri"><a class="col-w" href="/site/guide?pid=<?=$product->id?>">规则</a></li>
            </ul>
            <div style="height: .45rem; width: 100%;"></div>
            <div class="mask"></div>
            <ul class="pro-name">
                <?php foreach ($productArr as $key => $value): ?>
                    <a href="/site/detail?pid=<?=$value['id']?>&type=<?=session('sim_type')?>"><li class="col-5"><?=$value['name']?></li></a>
                     <?php endforeach ?>
                     <?php if (!empty(session('sim_type'))):?>
                        <a href="/site/detail"><li class="col-5">[返回实盘交易]</li></a>
                        <?php endif?>
                    
            </ul>
            
        <!--折线图TAB-->
            <ul class="tr-tab flex col-1 <?=$type?>">
                <li data-name="mLine">分时<span class="active"></span></li>
                <li data-name="dayLine">全天<span></span></li>
                <li data-name="lightnLine">闪电<span></span></li>
                <li data-name="pan">盘口<span></span></li>
                <li data-name="kLine">日线<span></span></li>
                <li data-name="mKLine">分钟<i class="iconfont"></i><span></span>
                    <ul class="tr-tab2 none">
                        <li data-name="mKLine1">1M</li>
                        <li data-name="mKLine5">5M</li>
                        <li data-name="mKLine15">15M</li>
                        <li data-name="mKLine30">30M</li>
                        
                    </ul>
                </li>
            </ul>
            <!--折线图-->
            <div id="mLine" class="chartBox" data-highcharts-chart="0"><div id="highcharts-xu5i3u5-0" class="highcharts-container " style="position: relative; overflow: hidden; width: 1920px; height: 382px; text-align: left; line-height: normal; z-index: 0; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></div></div>
            <div id="dayLine" class="none chartBox"><div style="text-align: center;"><span class="round"></span><span class="text">图表加载中...</span></div></div>
            <div id="lightnLine" class="none chartBox"><div style="text-align: center;"><span class="round"></span><span class="text">图表加载中...</span></div></div>
            <div id="kLine" class="none chartBox"><div style="text-align: center;"><span class="round"></span><span class="text">图表加载中...</span></div></div>
            <ul id="pan" class="none col-1 chartBox">
                    <li>最新<em data-pan-indices="" data-color="price" class="fr col-down"><?=$newData->price?></em></li>
                    <li>涨幅<em data-pan-change="" data-end="%" data-color="0" class="fr col-down"><?=$newData->diff_rate?></em></li>
                    <li>现手<em data-pan-tradevol="" class="fr col-up">-</em></li>
                    
                    <li>涨跌<em data-pan-changevalue="" data-color="0" class="fr col-down"><?=$newData->diff?></em></li>
                    <li>总手<em data-pan-totalqty="" class="fr col-up">-</em></li>
                    <li>最高<em data-pan-high="" class="fr col-up"><?=$newData->high?></em></li>
                    <li>持仓<em data-pan-volume="" class="fr col-up">-</em></li>
                    <li>最低<em data-pan-low="" class="fr col-down"><?=$newData->low?></em></li>
                    <li>开盘<em data-pan-open="" data-color="price" class="fr col-down"><?=$newData->open?></em></li>
                    <li>涨停<em data-pan-limitupprice="" class="fr col-up">-</em></li>
                    <li>振幅<em data-pan-swing="" data-end="%" data-color="0" class="fr col-up">-</em></li>
                    <li>跌停<em data-pan-limitdownprice="" class="fr col-down">-</em></li>
                    
            </ul>
                        <!--用户资金-->
            <ul class="tr-head flex col-1 <?=$type?>">
                <li class="col-2">可用资金 <span id="money" class="col-1"><?=$user->account-$user->blocked_account?></span></li>
                <li class="col-2">金券 <span id="bond" class="col-1">0</span> 张</li>
                <li><button onclick="location.href=&#39;<?= url(['user/recharge', 'user_id' => u()->id]) ?>&#39;" class="btn-white">充值</button></li>
            </ul>
        <!--产品内容-->
            <div class="tr-content flex2">
                <span class="le flex2 pr">
                    <div>
                        <div class="col-1" style="font-size: .12rem;position: absolute;top: .35rem;"><?=$product->name ?>/
                            <em data-pan-proNo="" style="font-size:.1rem"><?=$product->table_name ?></em>
                        </div>
                        <?php $class='up'; if($newData->price-$newData->open<0){$class='down';}?>
                        <em data-pan-indices="" data-color="price" class="col-<?=$class?>"><?=$newData->price?></em>
                    </div>
                    <span>
                        <p data-pan-changevalue="" data-color="0" class="col-<?=$class?>"><?=$newData->diff?></p>
                        <p data-pan-change="" data-end="%" data-color="0" class="col-<?=$class?>"><?=$newData->diff_rate?></p>
                    </span>
                    <!-- <em id="nowPriceShow" class="col-up"></em><p><span id="udPrice" class="col-up"></span><span id="udPercent" class="col-up"></span></p> -->
                </span>
                <span class="ri col-1" id="secDataForm">
                    <span class="col-2 f-ri"><i class="buy-in"><i data-ratio-buyvol="" style="width: 6%;"></i></i>买入<em data-pan-buyvol="" class="col-up " style="width:.2rem;display:inline-block;">6</em></span>
                    <span class="col-2 f-ri"><i class="sale-out"><i data-ratio-sellvol="" style="width: 3%;"></i></i>卖出<em data-pan-sellvol="" class="col-down" style="width:.2rem;display:inline-block;">3</em></span>
                </span>
            </div>  

        <!--购买按钮-->
            <ul class="buy-btn flex <?=$type?>">
                <li class="other flex-c btn-hui2 pr"><a class="btn-le" href="<?= url('user/holdStock')?>?type=<?=$type=='moni'?'sim':''?>">持仓</a></li>
                <li class="other flex-c btn-hui2 pr"><a class="btn-le" href="<?= url('user/transDetail')?>?type=<?=$type=='moni'?'sim':''?>">结算</a></li>
                <li class="up goBuy flex-c btn-up"><a data-url="<?=url('site/buyMiddle')?>?url=<?= url('site/buyProduct')?>?pid=<?=$product->id?>&state=up"><p data-pan-sell=""></p>买涨</a></li>
                <li class="down goBuy flex-c btn-down"><a data-url="<?=url('site/buyMiddle')?>?url=<?= url('site/buyProduct')?>?pid=<?=$product->id?>&state=down"><p data-pan-buy=""></p>买跌</a></li>
            </ul>
        </div>
        <script src="/test/highstock.js"></script>
        <script src="/test/lineData.js"></script>
        <script type="text/javascript">
         var chartColor = $(".index-head").css("background-color")
        </script>
        
        <script src="/test/jquery.cookie.js"></script>
        <script>
        $(function(){

            //setInterval(queryStockInfo, 1000);//启动定时器，更新产品信息
            //queryStockInfo();
            // 获取用户余额

            /*$.ajax({
                    url : '/member/bond',
                    data: {
                        productId: chart.data.productId
                    },
                    success : function(data) {
                        $('#bond').text(data.length);
                    }
                })*/
            
                //商品列表显示
                var btnMid = $(".index-head").find(".mid"),
                    mask = $(".mask"),
                    proList = $(".pro-name");
                
                btnMid.on("click",function(){
                        proList.toggleClass("active")
                        mask.toggleClass("active")
                })
                mask.on("click",function(){
                        proList.removeClass("active")
                        mask.removeClass("active")
                })
                
                
                var tab = $('.tr-tab li');
                tab.on('click', function(event){
                    
                    var name = $(this).data('name');
                    var fn = 'mKLine';
                    if(name && name.indexOf(fn) > -1){
                        if(name != fn){
                            var arg = name.replace(fn, '')
                            tab.find("span").removeClass('active');
                            $(this).parent().parent().find('span').addClass('active')
                            $('.chartBox').addClass('none');
                            $('#kLine').removeClass('none');
                            line(chart.kLine, [arg])
                        }
                        $('.tr-tab2').slideToggle(250)
                    }else{
                        if(!$(this).find("span").hasClass("active")){
                            tab.find("span").removeClass('active');
                            $(this).find("span").addClass('active');
                            $('.chartBox').addClass('none');
                            
                            $('#' + name).removeClass('none');
                            if($('#' + name).text() == '图表加载中...' || name == 'kLine')
                                line(eval('chart.' + name), [0])
                        }
                        $('.tr-tab2').slideUp(250)
                    }
                    event.stopPropagation() 
                    
                })
                
                function line(fn, args){
                    if(typeof(fn) == 'function')
                        fn.apply(this, args)
                }
                
                chart.init('mLine')
                
            
            $('[data-url]').on('click', function(){
                var agreement = $.cookie('yht-0-member-agreement-' + 'MHI');
                var url = $(this).data('url');
                if(url == ''){
                    return false
                }
                /*if(!agreement){
                    url = 'signed?simulator=0';
                    $.cookie('member-agreement-url', $(this).data('url'), {path:'/', expires:300});
                    $.cookie('member-agreement-proNo', 'MHI', {path:'/', expires:300})
                }*/
                location.href = url;
            })

             function queryStockInfo(){
                var proNos='<?=$product->table_name ?>';//商品编号
                var newprice=$('[data-pan-indices]');//最新价格
                var changevalue=$('[data-pan-changevalue]');//涨跌值
                var change=$('[data-pan-change]');//涨跌百分比

            $.ajax({

                url: '<?= url('site/stockInfo')?>',
                data: {
                    proNo: proNos,
                    pid:<?=$product->id?>
                },
                success: function(data){
                    var obj = data.data.product;
                    var info=data.info;
                    if(data.state)
                    {
                        $('[data-pan-buyvol]').html(obj['buy']);
                        $('[data-pan-sellvol]').html(obj['sell']);
                        $('[data-ratio-buyvol]').css('width',obj['buy']+'%');
                        $('[data-ratio-sellvol]').css('width',obj['sell']+'%');
                        $('[data-pan-sell]').html(obj['sellprice']);
                        $('[data-pan-buy]').html(obj['buyprice']);

                        if(obj['price']>obj['close'])
                       {
                        newprice.addClass('col-up');
                        changevalue.addClass('col-up');
                        change.addClass('col-up');
                        changevalue.removeClass('col-down');
                        change.removeClass('col-down');
                        newprice.removeClass('col-down');
                        newprice.html(obj['price']);
                        changevalue.html(obj['diff']);
                        change.html(obj['diff_rate']);


                       }
                     else
                       {
                        newprice.addClass('col-down');
                        changevalue.addClass('col-down');
                        change.addClass('col-down');
                        changevalue.removeClass('col-up');
                        change.removeClass('col-up');
                        newprice.removeClass('col-up');
                        newprice.html(obj['price']);
                        changevalue.html(obj['diff']);
                        change.html(obj['diff_rate']);

                       }
                    }





                }
            })
        }
        })
        </script>
    
</body></html>