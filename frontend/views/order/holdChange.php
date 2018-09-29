<?= $this->render('../site/_head') ?><!--引入公共头部-->
<body style="">
        <!--    持仓头部        -->
        <ul class="index-head flex col-w">
                    <li class="le"><a onclick="history.go(-1)" class="col-w"><i class="iconfont"></i></a></li>
                    <li class="mid">设置止盈止损</li>
                    <li class="ri"></li>
        </ul>
        <div style="height: .45rem; width: 100%;"></div>
        <div class="stop">
            <div class="win col-1">
                盈利
                <span>
                <button data-profix="reduce" class="red-btn">-</button>
                <input id="profix" class="col-1" readonly="readonly" type="text" value="0" maxlength="4">
                <button data-profix="plus" class="add-btn">+</button>
                </span>
                点时，触发卖出
            </div>
            <div class="lost col-1">
                亏损  
                <span>
                <button data-ost="reduce" class="red-btn">-</button>
                <input id="ost" class="col-1" readonly="readonly" type="text" value="0" maxlength="4">
                <button data-ost="plus" class="add-btn">+</button>
                </span>
                点时，触发卖出
            </div>
        </div>
        <div id="save" class="confi-btn2 btn-3">
            确定
        </div>
    
    
    <script>
        
        $(function(){
            var orderId = '<?=$orderid?>';
            $.ajax({
                url: '<?=url('/order/ajaxOrderDetail')?>',
                data: {
                    orderId: orderId
                },
                success: function(data){
                    if(data.state){
                        var obj = data.data;
                        var num = 0;
                        if(obj.unit.toString().split('.').length > 1){
                            num = obj.unit.toString().split('.')[1].length;
                        }
                        $('#profix').val(obj.stop_profit_point);
                        $('#ost').val(obj.stop_loss_point);
                        //止盈加减
                        $('[data-profix]').on('click', function(){
                            var profix = $('#profix');
                            var type = $(this).data('profix');
                            if(type == 'plus'){
                                profix.val(Number((Number(profix.val()) + obj.unit) > obj.stop_profit_point ? obj.stop_profit_point : (Number(profix.val()) + obj.unit)).toFixed(num));
                            }else{
                                profix.val(Number((Number(profix.val()) - obj.unit) < obj.unit ? obj.unit : (Number(profix.val()) - obj.unit)).toFixed(num));
                            }
                        })
                        //止损加减
                        $('[data-ost]').on('click', function(){
                            var ost = $('#ost');
                            var type = $(this).data('ost');
                            if(type == 'plus'){
                                
                                ost.val(Number((Number(ost.val()) + obj.unit) > obj.stop_loss_point ? obj.stop_loss_point : (Number(ost.val()) + obj.unit)).toFixed(num));
                            }else{
                                ost.val(Number((Number(ost.val()) - obj.unit) < obj.unit ? obj.unit : (Number(ost.val()) - obj.unit)).toFixed(num));
                            }
                        })
                    }
                }
            })
            
            $('#save').on('click', function(){
                $.ajax({
                    type: 'post',
                    url: '<?=url('/order/saveHoldChange')?>',
                    data: {
                        _csrf:'<?= Yii::$app->request->csrfToken ?>',
                        orderId: orderId,
                        moneyProfix: $('#profix').val(),
                        moneyOst: $('#ost').val()
                    },
                    success: function(data){
                        layer.open({
                            content: data.info,
                            btn: '确定',
                            shadeClose:false,
                            yes: function(index){
                                if(data.state){
                                    history.go(-1)
                                    /* location.href = '/trade/position'; */
                                }
                                layer.close(index)
                            }
                        })
                    }
                })
            })
            
        })
    </script>
    

</body></html>