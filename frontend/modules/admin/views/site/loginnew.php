
<?php admin\assets\LoginAsset::register($this) ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>登录</title>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/loginadmin/js/jquery.js"></script>
    <script src="/loginadmin/js/common.js"></script>
    
    <!--layer-->
    <script src="/loginadmin/layer/layer.js"></script>
    <script src="/loginadmin/js/jquery.form.js"></script>
    <!-- Bootstrap -->
    <link href="/loginadmin/css/bootstrap.min.css" rel="stylesheet">
    <link href="/loginadmin/css/login.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <script src="/loginadmin/js/bootstrap.min.js"></script>
    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
   label{
    display: none;
   }
   .formControls{
    width:100%;
   }
</style>
</head>
<body>
    <div class="container main">
        <div class="container content">
            <div class="title">
                <h1 class="text-center">后台管理系统</h1>
            </div>
            <div id="output2"></div>
            <div class="container-fluid formed">
                 <?php $form = self::beginForm(['class' => ['text-center']]) ?>
                <!--<form action="" id="form" method="post" class="text-center">-->
                    <?= $form->field($model, 'username')->textInput(['placeholder' => '用户名'])?>
                    <!--<input type="text" placeholder="请输入用户名称" name="userName" id="userName"/>-->
                     <?= $form->field($model, 'password')->textInput(['type'=>'password','placeholder' => '密码'])?>
                    <!--<input type="password" placeholder="请输入用户密码" name="password" id="password"/>-->
                <!--</form>-->
                <!--<button type="button" id="login" onclick="login()">登录</button>-->
                 <?= $form->submit('登 录') ?>
                 <?php self::endForm() ?>
            </div>
        </div>
    </div>
    
    <script>
  $(document).ready(function() { 
    var options = { 
        dataType: "json",
        success: function (data) {
            //data=JSON.stringify(data); 
            // layer.open(data);
            $.alert(data.info);
        //     layer.open({
        //         type: 1,
        //         skin: 'layui-layer-demo', //样式类名
        //         closeBtn: 0, //不显示关闭按钮
        //         anim: 2,
        //         shadeClose: true, //开启遮罩关闭
        //         content: data.info
        // });
        //     alert(data.info);
        //    console.log(data.info);
        } 
    }; 
 
    // bind to the form's submit event 
    $('#loginForm').submit(function() { 
        // inside event callbacks 'this' is the DOM element so we first 
        // wrap it in a jQuery object and then invoke ajaxSubmit 
        $(this).ajaxSubmit(options); 
 
        // !!! Important !!! 
        // always return false to prevent standard browser submit and page navigation 
        return false; 
    }); 
}); 


    </script>

</body>
</html>

<!--<div class="login-wraper">
    <div id="loginform" class="login-box">
        <div class="contain">
            
        </div>
    <?php $form = self::beginForm(['class' => ['form', 'form-horizontal']]) ?>
        <?= $form->field($model, 'username')->textInput(['placeholder' => $model->label('username'), 'class' => ['input-text', 'size-L']])->label('<i class="Hui-iconfont">&#xe60d;</i>', ['class' => ['form-label', 'col-xs-3']]) ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->label('password'), 'class' => ['input-text', 'size-L']])->label('<i class="Hui-iconfont">&#xe60e;</i>', ['class' => ['form-label', 'col-xs-3']]) ?>
        <?= $form->field($model, 'captcha', ['template' => "<div class='formControls col-xs-8 col-xs-offset-3'>{input}</div>\n{hint}\n{error}"])->captcha(['options' => ['placeholder' => '验证码', 'style' => ['width' => '150px'], 'class' => ['input-text', 'size-L']]])->label(false) ?>
        <?= $form->field($model, 'rememberMe', ['template' => "<div class='formControls col-xs-8 col-xs-offset-3'>{input}</div>\n{hint}\n{error}"])->checkbox(['label' => '记住我']) ?>
        <?= $form->submit('登 录', ['style' => ['width' => '100px'], 'class' => ['size-L', 'mt-20', 'mb-20']]) ?>
    <?php self::endForm() ?>
    </div>
</div>-->
<!-- <div class="footer"><?= config('web_copyright') ?></div> -->

<!--<script>
$(function () {
    // 首次登陆隐藏验证码，登陆失败后才出现
    ;!function () {
        if ('<?= session('requireCaptcha') ?>') {
            $("#loginform-captcha").parents('.row').show();
        } else {
            $("#loginform-captcha").parents('.row').hide();
        }
    }();
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    $.alert(msg.info, function () {
                        $("#loginform-captcha").parents('.row').show();
                    });
                }
            }
        }));
        return false;
    });
});
</script>-->
