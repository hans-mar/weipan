<?= $this->render('_head') ?><!--引入公共头部-->
 <body> 
  <!--  帮助头部        --> 
  <ul class="index-head flex col-w"> 
   <li class="le"><a onclick="history.go(-1)" class="col-w"><i class="iconfont"></i></a></li> 
   <li class="mid"><?=$product->name?>交易规则</li> 
   <li class="ri"></li> 
  </ul> 
  <div style="height: .45rem; width: 100%;"></div> 
  <div class="warning  col-1"> 
   <div class="col-1 col-6">
    <ul class="rule-list">
     <li>
        <span class="rule-list2">交易品种</span>
        <span class="rule-list2"><?=$product->name?></span>
     </li>
     <li>
        <span>货币单位</span>
        <span><?=$desc[0]?></span>
     </li>
     <li>
        <span class="rule-list2">交易单位</span>
        <span class="rule-list2"><?=$desc[1]?></span>
     </li>
     <li>
        <span>最小波动</span>
        <span><?=$desc[2]?></span>
     </li>
     <li>
        <span class="rule-list2">波动盈亏</span>
        <span class="rule-list2"><?=$desc[3]?></span>
     </li>
     <li>
        <span style="height: 1.8rem;padding-top: .8rem;">交易时间</span>
        <span style="height: 1.8rem;padding-top: .16rem;">
        <?=$desc[4]?>
        </span>
     </li>
     <li>
        <span class="rule-list2" style="height: 1rem;padding-top: .4rem;">强制平仓时间</span>
        <span class="rule-list2" style="height: 1rem;padding-top: .3rem;">
        <?=$desc[5]?></span>

     </li>
     <li>
        <span>交易综合费</span>
        <span><?=$product->fee?>元/手</span>
     </li>
     <li>
        <span class="rule-list2" style="height:.8rem;padding-top: .28rem;">汇率</span>
        <span class="rule-list2" style="height: .8rem;padding-top: .18rem;">

            <?php if($product->currency<>"人民币"):?>
            <p><?=$desc[6]?></p><p>（汇率波动较大时，将会进行调整）</p>
        <?php endif?>
        </span>
     </li>
    </ul>
    <div class="cb"></div>
    <p><?=$product->name?>期货，<?=$desc[1]?>，最小波动<?=$desc[7]?>（相当于最小波动盈亏人民币<?=$desc[8]?>） ，可买涨买跌。</p>
    <br />
    <p class="rule-title">出入金规则</p>
    <p><span style="font-family: 微软雅黑,Microsoft YaHei;font-size: 12px;">入金时间：全天24小时</span></p>
    <p><span style="font-family: 微软雅黑,Microsoft YaHei;font-size: 12px;">
    出金时间：9：00-18:00，18:00后提现隔天到账；到账时间以银行为准，节假日顺延。</span></p>
    <p><span style="font-family: 微软雅黑,Microsoft YaHei;font-size: 12px;"><br /></span></p>
    <p><span style="font-family: 微软雅黑,Microsoft YaHei;font-size: 12px;">提现手续费：入金有交易，单笔提现收取2元手续费；若入金未交易，单笔提现收取1%的手续费。</span></p>
    <p><span style="font-family: 微软雅黑,Microsoft YaHei;font-size: 12px;"></span></p>
    <p class="rule-title">什么是做多（买涨）？</p>
    <p class="rule-content">当您进行做多（买涨）交易时，若价格上涨，账面为盈利状态；若价格下跌，账面则为亏损状态！</p>
    <p><br /></p>
    <p class="rule-title">什么是做空（买跌）？</p>
    <p class="rule-content">当您进行做空（买跌）交易时，若价格下跌，账面为盈利状态；若价格上涨，账面则为亏损状态！</p>
    <p class="rule-title">什么是止盈？<br /></p>
    <p class="rule-content">当单笔交易盈利金额触发（多于等于）指定的止盈金额时，该笔交易会被强制平仓。</p>
    <p class="rule-content">由于市场的价格实时都在变动，不保证平仓后最终盈利金额一定大于等于止盈金额，有可能会小于触发的止盈金额。</p>
    <br />
    <p class="rule-title">什么是止损？</p>
    <p class="rule-content">当单笔交易亏损金额触发（多于等于）指定的止损金额时，该笔交易会被强制平仓。</p>
    <p class="rule-content">由于市场的价格实时都在变动，不保证卖出后最终亏损金额一定小于等于止损金额，有可能会大于止损金额。</p>&nbsp;&nbsp; 
    <br />
    <p class="rule-title">什么是持仓时间？</p>
    <p class="rule-content"><?=$product->name?>期货最后持仓时间：</p>
    <p class="rule-content"><?=$desc[5]?></p>
    <p class="rule-content">当持仓时间到点后，持仓中的交易会被强制平仓，不保证成交价格，请务必在到期前自己选择卖出。<br /></p>
    <p class="rule-title">交易综合费</p>
    <p class="rule-content"><?=$product->name?>期货每手交易综合费：<?=$product->fee?>元（单向收取）<br /></p>
    <p class="rule-content"><br /></p>
    <p class="rule-title">履约保证金</p>
    <p class="rule-content">履约保证金为操盘手委托平台冻结用于履行交易亏损赔付义务的保证金。操盘手以冻结的履约保证金作为承担交易亏损赔付的上限。多出上限部分的亏损全部由合作的投资人承担。<br /></p>
    <p class="rule-content"><br /></p>
    <p class="rule-title">实盘交易下单<br /></p>
    <p class="rule-list2" style="padding: .1rem;">您所有的<?=$product->name?>交易，全部通过期货公司的API通道，下单到交易所进行对手价撮合成交。</p>
   </div> 
  </div> 
 </body>
</html>