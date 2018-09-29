<?php use common\helpers\Html; ?>
<?php common\assets\HighStockAsset::register($this) ?>
<?php $this->regJs('candle') ?>
<?php $this->regCss('jiaoyi') ?>
<?php $this->regCss('trade') ?>
<?php $this->regCss('geren') ?>
<?php $this->regCss('common.css') ?>
<!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
<style type="text/css">
	body{
		margin: 0;
		padding: 0;
	}
	.demo{
		margin:10rem auto;
		text-align: center;
	}
	.demo .font_qr{
		width: 25rem;
		height: 3.5rem;
		color: #fff;
		font-size: 15px;
		font-weight: bold;
		line-height:3.5rem;
		border-radius: 30px;
		text-align: center;
		margin: 0 auto;
		background-color: #00AAEE;
	}
	#code{
		margin-top:10px;
	}
	.demo img{
		margin-top:10px;
		text-align: center;
		background-color: #00AAEE;
	}
</style>
	
	<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
	<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
	<script type="text/javascript" src="/js/jquery.qrcode.min.js"></script>
	 
	<script type="text/javascript">
		$(function(){
			var str = "<?=$ursl;?>";
			//if(is_weixin()){
				//默认使用Canvas生成，并显示到图片   
				 var qrcode= $('#code').qrcode(str).hide();   
				 var canvas=qrcode.find('canvas').get(0);  
				 $('#imgOne').attr('src',canvas.toDataURL('image/jpg')); 
				//$('#code').qrcode(str);
			// }else{
			// 	window.location.href = str;
			// }
			
		})

		function is_weixin() { 
			var ua = window.navigator.userAgent.toLowerCase(); 
			if (ua.match(/MicroMessenger/i) == 'micromessenger') { 
				return true;
				//$("#rs").text("微信浏览器"); 
			} else { 
				return false;
			   //$("#rs").text("不是微信浏览器"); 
			} 
		}

	</script>
	<div id="main">
	   <div class="demo">
			<div class="font_qr"><?=$tip;?></div>
			<div id="code"></div>
			<div><img id='imgOne' /></div>    
	   </div>
	</div>