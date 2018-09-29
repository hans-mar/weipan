<?= $this->render('_head') ?><!--引入公共头部-->
	<body>
			<ul class="index-head flex col-w">
					<li class="le"><a href="/site/login" class="col-w"><i class="iconfont"></i></a></li>
					<li class="mid">重设密码</li>
					<li class="ri"></li>
			</ul>
			<div style="height: .45rem; width: 100%;"></div>
			
			<form class="register">
				<input type="hidden" name="action" value="1"></input>
				<div class="li-box">
					<span class="col-1">手机号：</span>
					<input id="mobile" name="mobile" class="col-1" type="tel" class="user-input" maxlength="30" placeholder="请输入手机号">
				</div>
                <div class="li-box">
					<span class="col-1">重设密码：</span>
					<input id="password" name="password" class="col-1" type="password" class="user-input" maxlength="30" placeholder="请输入密码">
				</div>
                <div class="li-box">
					<span class="col-1">重复重设密码：</span>
					<input id="repassword" name="repassword" class="col-1" type="password" class="user-input" maxlength="30" placeholder="请再次输入密码">
				</div>
				<div class="li-box pr">
					<span class="col-1">短信验证：</span>
					<input id="verifyCode" name="verifyCode" class="col-1" type="tel" class="user-input" maxlength="30" placeholder="请输入短信验证码">
					<button type="button" id="Obtain" class="get-code btn-orange pa btn-hui">获取验证码</button>
				</div>
				<!--<input name="rid" id="rid" type="hidden"/>-->
			</form>
			 <!--<p style="margin:.15rem .15rem ; font-size: .13rem;" class="col-2"><input id="agreement" name="agreement" style="vertical-align: middle;" type="checkbox" checked />我已阅读并同意<a href="service" class="col-up">《易汇通服务协议》</a></p>-->
			
			<div id="next" class="confi-btn2 btn-blue2 confi-btn-hui">
				重设密码
			</div>
           
	</body>
	<script src="/test/jquery.cookie.js"></script>
	<script>
	
	function getQueryString(name)
	{
	     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	     var r = window.location.search.substr(1).match(reg);
	     if(r!=null)return  unescape(r[2]); return null;
	}
		$(function(){
			// var rid = getQueryString("rid");
			// console.log("rid:"+rid)
	  //   	var COOKIE_NAME = 'yht_rid';
	  //   	if(rid != null && rid != ''){
	  //   		$.cookie(COOKIE_NAME, rid, {path:'/', expires:3});
	  //   		$("#rid").val(rid);
	  //   	}else{
	  //   		if($.cookie(COOKIE_NAME)){
	  //       		rid = $.cookie(COOKIE_NAME);
	  //       	}
	  //   		$("#rid").val(rid);
	  //   	}
			
			
			
			var verificationData = {
					mobile: false,
					vcode: false,
					verifyCode: false,
					//agreement: true	
			};
			
			
			
			
			function verification(){
				var flag = true;
				if(verificationData.vcode){
					$('#Obtain').removeClass('btn-hui');
				}else{
					$('#Obtain').addClass('btn-hui');
				}
				for(var i in verificationData){
					if(!verificationData[i]){
						flag = false;
					}
				}
				if(flag){
					$('#next').removeClass('confi-btn-hui');
				}else{
					$('#next').addClass('confi-btn-hui');
				}
				
			}
			
			
			
			var mobileV = /^1[34578]\d{9}$/;
			$('#mobile').on('blur', function(){
				var me = $(this);
				if(!mobileV.test($(this).val())){
					
					layer.open({
								content: '手机号格式错误，请重新输入！',
								btn: '确定',
								yes: function(index){
									me.val('');
									me.focus();
									layer.close(index)
								}
							})
				}else{
                    verificationData.mobile = true;
                    verificationData.vcode = true;
					verification()
				}
			})
			$('#mobile').on('keyup', function(){
				if(!mobileV.test($(this).val())){
					verificationData.mobile = false;
				}else{
					verificationData.mobile = true;
				}
				verification()
			})
			
			// $('#vcode').on('keyup', function(){
			// 	var vcode = $(this).val();
			// 	if(vcode.length == 4){
			// 		$.ajax({
			// 			url: 'checkVCode',
			// 			data: {
			// 				vcode: vcode
			// 			},
			// 			success: function(data){
							
			// 				if(data.success){
								
			// 					verificationData.vcode = true;
			// 					verification()
			// 				}
			// 			}
			// 		})
			// 	}else{
			// 		verificationData.vcode = false;
			// 		verification()
			// 	}
				
			// })
			//获取验证码
			$('#Obtain').on('click', function(){
				
				if(!$(this).hasClass('btn-hui')){
					var msg = '短信将发送到' + $('#mobile').val();
					layer.open({
						content: msg,
						btn: ['确定', '取消'],
						yes: function(index){
							layer.close(index)
							
							$.ajax({
								url: '/site/verify-code',
								data: {mobile:$('#mobile').val()},
								success: function(data){
									layer.open({
										content: data.info,
										btn: '确定',
										yes: function(index){
											layer.close(index)
										}
									})
								}
							})
						}
					})
					
				}
			})
			
			
			$('#verifyCode').on('keyup', function(){
				var code = $(this).val();
				if(code.length >= 4){
					verificationData.verifyCode = true;
				}else{
					verificationData.verifyCode = false;
				}
				verification()
			})
			
			// $('#agreement').on('change', function(){
			// 	if($(this).is(':checked')){
			// 		verificationData.agreement = true;
			// 	}else{
			// 		verificationData.agreement = false;
			// 	}
			// 	verification()
			// })
			//重设密码
			$('#next').on('click', function(){
				$.ajax({
					type: 'post',
					url: '/site/ajax-forget',
					data: $('form').serializeArray(),
					success: function(data){
							layer.open({
								content: data.info,
								btn: '确定',
								yes: function(index){
									
									layer.close(index)
								}
							})
						
						
					}
				})
			})
			
			
			
			
			
			
		})
	</script>
</html>
