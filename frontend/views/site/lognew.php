 <!DOCTYPE html>
<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
        <meta name="keywords" content="股指期货,恒指">
        <meta name="description" content="<?=config('web_name')?>,恒指">
        <title><?=config('web_name')?></title>
        <link rel="stylesheet" href="/test/base.css?r=20170520">
        <link rel="stylesheet" href="/test/main.css?r=20170520">
        <link rel="stylesheet" href="/test/main-blue.css?r=20170520">
        <script src="/test/jquery.js"></script>
        <script src="/test/public.js"></script>
        <script src="/test/clipboard.min.js"></script>
        <script src="/test/fastclick.js"></script>
        <script src="/loginadmin/js/common.js"></script>
    
    <!--layer-->
    <script src="/loginadmin/layer/layer.js"></script>
    <script src="/loginadmin/js/jquery.form.js"></script>
        <script>
            $(function() {  
                FastClick.attach(document.body);  
            });  
        </script>
</head>
	<body style="">
		<div class="register-all">
			<ul class="index-head flex col-w">
					<li class="le"></li>
					<li class="mid">登录</li>
					<li class="ri"><a class="col-w" href="<?=url('/site/reg')?>"></a></li>
			</ul>
			<div style="height: .45rem; width: 100%;"></div>
            <?php $form = self::beginForm(['showLabel' => false]) ?>
			<ul class="ul-box first sign">
				<li>
					
                    <?= $form->field($model, 'username')->textInput(['placeholder' => '手机号']) ?>
				<!--<input id="mobile" class="col-1" type="tel" maxlength="30" placeholder="请输入手机号" style="width:80%">-->
				</li>
				<li>
				
                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => '密码']) ?>
				<!--<input id="password" class="col-1" type="password" maxlength="30" placeholder="请输入密码" style="width:80%">-->
				</li>
			</ul>
			<div class="cent">
				<button id="login" class="confi-btn2 btn-1">登录</button>
				<p style="margin-top: .2rem;"><a class="col-2" href="<?=url('/site/reg')?>">注册新用户</a></p>
				<p style="margin-top: .2rem;"><a class="col-2" href="<?=url('/site/passForGet')?>">忘记密码？</a></p>
			</div>
            <?php self::endForm() ?>
		</div>
		
	
	
	<script>
		 $(document).ready(function() { 
    var options = { 
        dataType: "json",
        success: function (data) {

            $.alert(data.info);
        
        } 
    }; 
 
    
    $('#loginForm').submit(function() { 
      
        $(this).ajaxSubmit(options); 

        return false; 
    }); 
}); 
    </script>
	
</body></html>