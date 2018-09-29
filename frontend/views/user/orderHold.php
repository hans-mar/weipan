<?= $this->render('../site/_head') ?><!--引入公共头部-->
<body style="">
        <!--    持仓头部        -->
        <ul class="index-head flex col-w <?=session('sim_type')=='sim'?'moni':''?>">
                    <li class="le"><a onclick="history.go(-1)" class="col-w"><i class="iconfont"></i></a></li>
                    <li class="mid position-tab <?=session('sim_type')?>"><div><a class="active">持仓</a><a onclick="location.replace('trans-detail?type=<?=session('sim_type')=='sim'?'sim':''?>')">结算</a></div></li>
                    <li class="ri"></li>
        </ul>
        <div style="height: .45rem; width: 100%;"></div>
        
        <!--    持仓内容        -->
        <ul id="container" class="position-list">
            <li style="display: none">
                <div class="list-top col-1"><span></span><button class="fr btn-white"><a>设置止盈止损</a></button></div>
                <div class="list-bottom flex col-1"><span class="left"><em class=""></em><em class=""></em><em></em><em></em><em></em><em></em></span><span class="right"><em class="col-2" style="display:inline-block;width: 100%;font-size:.1rem;margin-bottom: .02rem"></em><button id="sold" class="btn-1 fb">平仓</button></span></div>
            </li>

        </ul>
    
    
    <script>
        $(function(){
            //防止修改完成止盈止损后页面数据不刷新
            if(window.name != "reload"){
                 location.reload();
                 window.name = "reload";
            }else{
                 window.name = "";
            }
            
            
            
            //持仓列表容器
            var container = $('#container');
            //持仓商品载体
            var product = container.find('li').clone().removeAttr('style');
            
            
            //商品编码
            var proNos = [];
            //商品
            var list = [];
            
            container.on('click', '.fb', function(){
                var orderId = $(this).parent().parent().parent().data('orderId');
                layer.open({
                    content: '您是否确定卖出？',
                    btn: ['确定','取消'],
                    shadeClose:false,
                    yes: function(index){
                        $.ajax({
                            url: '<?=url('/order/ajaxSellPosition')?>',
                            data: {
                                orderId: orderId
                            },
                            success: function(data){
                                layer.open({
                                    content: data.info,
                                    btn: '确定',
                                    shadeClose:false,
                                    yes: function(index){
                                        layer.close(index)
                                    }
                                })
                            }
                        })
                        
                        
                        layer.close(index)
                    }
                })
                
                
                
            })
            loadList();
            //加载持仓列表
            function loadList(){
                //清空容器
                container.html('');
                list = [];
                $.ajax({
                    url: '<?= url('user/ajaxHoldStock')?>',
                    success: function(data){
                        if(data.state){
                            list = data.data;
                            $.each(list, function(){
                                proNos.push(this.product.table_name);
                                var node = product.clone();
                                node.attr('data-' + this.product.table_name, this.product.table_name);
                                node.data('unit', this.product.unit);
                                node.data('num', this.hand);
                                node.data('pointMoney', this.product.unit_price);
                                node.data('inPrice', this.price);
                                node.data('orderId', this.id);
                                node.data('type', this.rise_fall);
                                node.find('a').eq(0).attr('href', '/order/hold-change?orderid=' + this.id);
                                node.find('span').eq(0).text(this.product.name + '(' + this.product.table_name + ')');
                                node.find('em').eq(0).addClass(this.rise_fall == '1' ? 'col-up' : 'col-down').text((this.rise_fall == '1' ? '买涨' : '买跌') + '：' + this.hand + '手');
                                node.find('em').eq(2).text('止盈：' + this.stop_profit_point + '点');
                                node.find('em').eq(3).text('买入：' + this.price);
                                node.find('em').eq(4).text('止损：' + this.stop_loss_point + '点');
                                container.append(node);
                                node.find('em').eq(6).text(this.created_at);
                            })
                        }
                    }
                })
                
            }
            
            setInterval(function(){//持仓数量改变重新加载列表
                if(proNos.length > 0){
                    $.ajax({
                        url: '<?=url('/order/getHoldCount')?>',
                        success: function(data){
                            if(data.state){
                                if(data.data < list.length){
                                    loadList();
                                    /* location.reload(); */
                                }
                            }
                        }
                    })
                    
                    
                    $.ajax({
                        //ProPriceList
                        url: '<?= url('site/proPriceList')?>',
                        data: {
                            proNo: proNos.join(',')
                        },
                        success: function(data){
                            if(data.state){
                                var obj = data.data;
                                for(var proNo in obj){
                                    $('[data-' + proNo + ']').each(function(){
                                        $(this).find('em').eq(5).text('当前：' + obj[proNo]);
                                        var unit = 0;
                                        var unitNum = $(this).data('unit');
                                        if(unitNum.toString().split('.').length > 1){
                                            unitNum = unitNum.toString().split('.')[1].length;
                                        }else{
                                            unitNum = 0
                                        }
                                        if($(this).data('type') == '1'){
                                            unit = obj[proNo] - $(this).data('inPrice');
                                        }else{
                                            unit = $(this).data('inPrice') - obj[proNo];
                                        }
                                        unit = parseFloat(unit.toFixed(unitNum))
                                        var money = $(this).data('pointMoney') * unit / $(this).data('unit') * $(this).data('num');
                                        $(this).find('em').eq(1).removeClass().addClass(unit < 0 ? 'col-down' : 'col-up').text(unit + ' = ' + money.toFixed(2));
                                    })
                                }
                            }
                        }
                    })
                }
            }, 1000)
            
        })
    </script>
    

</body>
</html>
