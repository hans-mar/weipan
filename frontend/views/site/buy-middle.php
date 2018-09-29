<?= $this->render('_head') ?><!--引入公共头部-->
	<body>
		<ul class="index-head flex col-w">
					<li class="le"><a onclick="history.go(-1)" class="col-w"><i class="iconfont"></i></a></li>
					<li class="mid">签署协议</li>
					<li class="ri"></li>
		</ul>
		<div style="height: .45rem; width: 100%;"></div>
		<ul class="signing col-up">
			
			<li>《平台风险告知书》<button class="fr"><a href="/service/agreement.html">阅读</a></button></li>
		</ul>
		<div class="signing-agreement col-1">
			<div style="border: 1px #EA4C4C dashed; padding: .1rem;">
				<h3 style="margin-bottom: .05rem;">签署交易协议</h3>
				<p class="col-r" style="margin-bottom: .05rem;">现金实盘交易高风险提示</p>
				<p style="text-align: left;">期货具有高风险，并非适于所有的投资人。交易亏损金额也许会大于您的初始投资。交易期货之前，请仔细阅读相关规定及风险条款；平台使用保证金进行交易，仅适用于可承受高风险的投资人，交易之前请确保您具备风险承受能力。期货采用撮合制交易模式，投资者盈亏取决于其对期货市场多空行情判断和交易技术水平，平台仅作为交易工具提供方，投资过程中，可能出现因行情波动剧烈而导致实际亏损金额大于缴纳保证金金额，平台与投资者盈亏并无相关。期市有风险，投资需谨慎，适可而止，风险自担。如有异议，请不要参与交易！</p>
			</div>
		</div>
		<div style="height: .5rem;width: 100%;"></div>
		<button id="save" class="confirm btn-1">我已经阅读并同意签署</button>
	
<!-- 	<script src="http://spdj01.com/static/pc/js/jquery.cookie.js"></script>
	<script>
		$(function(){
			$('#save').on('click', function(){
				var proNo = $.cookie('member-agreement-proNo');
				$.cookie('yht-1-member-agreement-' + proNo, true, {path:'/', expires:300});
				location.replace($.cookie('member-agreement-url'));
			})
		})
	</script> -->
	<script>
	$(function(){
			$('#save').on('click', function(){
				// var proNo = $.cookie('member-agreement-proNo');
				// $.cookie('yht-1-member-agreement-' + proNo, true, {path:'/', expires:300});
				location.replace('<?=session('buyurl')?>');
			})
		})
	</script>
	 
</body></html>