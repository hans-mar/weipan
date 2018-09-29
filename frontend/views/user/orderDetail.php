<?= $this->render('../site/_head') ?><!--引入公共头部-->
<body> 
        <!--    结算头部        -->
        <ul class="index-head flex col-w <?=session('sim_type')=='sim'?'moni':''?>">
                    <li class="le"><a onclick="history.go(-1)"  class="col-w"><i class="iconfont">&#xe65d;</i></a></li>
                    <li class="mid position-tab <?=session('sim_type')?>"><div><a onclick="location.replace('hold-stock?type=<?=session('sim_type')=='sim'?'sim':''?>')">持仓</a><a class="active">结算</a></div></li>
                    <li class="ri"></li>
        </ul>
        <div style="height: .45rem; width: 100%;"></div>
        
        <!--    结算内容        -->
        <ul id="container" class="position-list settlement-list">
            <li style="display: none">
                <div class="list-top col-1"><span></span><em class="fr col-2"><p style="height: .09rem;margin-bottom: .03rem"></p><p style="height: .09rem"></p></em></div>
                <div class="list-bottom flex col-1"><span class="left"><em></em><em></em><em></em><em></em><em></em><em></em></span><span class="right"><p class="col-b">结算成功</p><p class="col-b"></p></span></div>
            </li>
        </ul>
        <p class="col-2 cent" style="font-size: .1rem;">近一个月的结算记录</p>
        <div id="loadMore" class="settlement-more col-1">查看更多</div>
    </body>
    
    <script>
        $(function(){
            
            var length = 1;
            //容器
            var container = $('#container');
            //克隆载体
            var pro = container.find('li').clone().removeAttr('style');
            //查看更多
            var loadMore = $('#loadMore');
            //清除无效载体
            container.html('');
            
            function query(){
                $.ajax({
                    url: '<?= url('user/ajaxTransDetail')?>',
                    data: {
                        p: length
                       
                    },
                    success: function(data){
                        length += 1;
                        var list = data.data;
                        if(list != null){
                            $.each(list, function(){
                                var node = pro.clone();
                                node.find('span').eq(0).text(this.product.name + '(' + this.product.table_name + ')');
                                node.find('p').eq(0).text(this.created_at);
                                node.find('p').eq(1).text(this.updated_at);
                                node.find('em').eq(1).addClass(this.rise_fall == '1' ? 'col-up' : 'col-down').text((this.rise_fall == '1' ? '买涨' : '买跌') + this.hand + '手');
                                node.find('em').eq(2).addClass(this.profit < 0 ? 'col-down' : 'col-up').text(this.profit + '元');
                                node.find('em').eq(3).text('止盈：' + this.stop_profit_point + '点');
                                node.find('em').eq(4).text('买入：' + this.price);
                                node.find('em').eq(5).text('止损：' + this.stop_loss_point + '点');
                                node.find('em').eq(6).text('卖出：' + this.sell_price);
                                var order_state = '手动平仓';
                                if(this.is_console=='1')
                                {
                                    order_state='系统平仓';
                                }
                                node.find('p').eq(3).text(order_state)
                                container.append(node);
                            })
                        }else{
                            loadMore.text('没有更多记录');
                            loadMore.off();
                        }
                    }
                })
            }
            
            
            loadMore.on('click', function(){
                query();
            })
            
            query()
            
            
            
        })
    </script>
</html>
