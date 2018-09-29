<?php $this->regCss('geren.css') ?>
<?php $this->regCss('main.css') ?>
<style type="text/css">
   
    
   
    .code.fr{
        position: absolute;
        height: 30px;
        line-height: 30px;
        right: 12px;
        top: 321px;
        color: #828284;
        background: transparent;
    }
    #submitBtn{
        color: #fff;
        font-size: 15px;
        background: #00E5B8;
        width: 90%;
        height: 44px;
        line-height: 44px;
        border-radius:6px;
        margin-left: 5%;
        margin-top: 60px;
        bottom:200px;
    }
</style>
<link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">
<div class='row-12 col-sm-10'>
	<form class="form-horizontal" role="form" id='form'   method="post" action='<?= url('kuaijie/confim') ?>' name='form'>
		<div class="form-group">
			<label for="firstname" class="col-sm-2 control-label">支付单号</label>
			<div class="col-sm-10">
				<input type="text"  readonly class="form-control" id="out_trade_no"  value="<?php echo $retSign['out_trade_no'];?>"  >	  
			</div>
		</div>
		<input type="hidden"  value="<?php echo $retSign['out_trade_no'];?>" name="out_trade_no" id="out_trade_no"/>
		<div class="form-group">
			<label for="lastname" class="col-sm-2 control-label">充值金额</label>
			<div class="col-sm-10">
				<input type="text"  readonly class="form-control" id="lastname" value='<?php echo $retSign['money'];?>'   placeholder="充值金额">
			</div>
		</div>
		<div class="form-group">
			<label for="lastname" class="col-sm-2 control-label">短信码</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="lastname" name='msmCode' id='msmCode'   placeholder="短信码">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				 <button type="submit" id="submitBtn" class=" col-xs-12 navbar-fixed-bottom text-center footer_bg font_16">提交</button>
			</div>
		</div>
	</form>
</div>  
<script>
$(function () {
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    $.alert(msg.info);
                } else {
                    $.alert(msg.info, function(){
                    window.location.href = '<?= url('user/index') ?>'
                        
                    });
                }
            }
        }));
        return false;
    });
    // 验证码
    // $("#verifyCodeBtn").click(function () {
    //     var mobile = $('.bank_mobile').val();
    //     var url = $(this).data('action');
    //     if (mobile.length != 11) {
    //         $.alert('您输入的不是一个手机号！');
    //         return false;
    //     }
    //     $.post(url, {mobile: mobile}, function(msg) {
    //           $.alert(msg.info);
    //     }, 'json');
    // })
});
</script>