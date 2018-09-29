$(function() {
    //数字累加累减
    $(".myContent").on("click", '.btn-coin', function() {
        //数字自加
        var hand = parseInt($('.hand').val());
        var max = parseInt($('.hand').data('max'));
        var num = parseInt($(this).data('value'));
        hand += num;
        if (hand < 1) {
            $(".btn-minute").addClass("unable-click");
            return;
        }else{
            $(".btn-minute").removeClass("unable-click");
        }
        if (hand > max) {
            $(".btn-add").addClass("unable-click");
            return;
        }else{
            $(".btn-add").removeClass("unable-click");
        }
        $('.hand').val(hand);
    });

    $(".myContent").on("click", '.btn-coined', function() {
        //数字自加
        var hand = parseInt($('.handed').val());
        var max = parseInt($('.handed').data('max'));
        var num = parseInt($(this).data('value'));
        hand += num;
        if (hand < 0) {
            $(".btn-minuteed").addClass("unable-click");
            return;
        }else{
            $(".btn-minuteed").removeClass("unable-click");
        }
        if (hand > max) {
            $(".btn-added").addClass("unable-click");
            return;
        }else{
            $(".btn-added").removeClass("unable-click");
        }
        $('.handed').val(hand);
        
    });
     $(".myContent").on("click", '.btn-coineded', function() {
        //数字自加
        var hand = parseInt($('.handeded').val());
        var max = parseInt($('.handeded').data('max'));
        var num = parseInt($(this).data('value'));
        hand += num;
        if (hand < 0) {
            $(".btn-minuteeded").addClass("unable-click");
            return;
        }else{
            $(".btn-minuteeded").removeClass("unable-click");
        }
        if (hand > max) {
            $(".btn-addeded").addClass("unable-click");
            return;
        }else{
            $(".btn-addeded").removeClass("unable-click");
        }
        $('.handeded').val(hand);
        
    });
    //合约定金和盈亏之间的切换
    $(".myContent").on("click", '.deposit>li', function() {
        $('.myContent .deposit>li').removeClass("active");
        $(this).addClass("active");
        // var pid = $(this).data('id');
        // $('.myContent .point>li').removeClass("active");
        // $('.myContent .point>li').each(function(){
        //     var id = $(this).data('id');
        //     if (id == pid) {
        //         $(this).addClass('active');
        //     }
        // });
        // tes($(this).data('id'));
    });
    //盈亏之间的切换
    $(".myContent").on("click", '.point>li', function() {
        $('.myContent .point>li').removeClass("active");
        $(this).addClass("active");
        // var pid = $(this).data('id');
        // $('.myContent .deposit>li').removeClass("active");
        // $('.myContent .deposit>li').each(function(){
        //     var id = $(this).data('id');
        //     if (id == pid) {
        //         $(this).addClass('active');
        //     }
        // });
        // tes($(this).data('id'));
    });

    //空白删除transaction1
    $(".myContent").on("click", '.removeClass', function() {
        $('.myContent').html('');
    });

    //取消按钮
    $(".myContent").on("click", '.cancel', function() {
        $('.myContent').html('');
    });

    //设置交易密码
    $(".myContent").on("click", '.setPassWord', function() {
        $.post("/site/ajax-set-password", {data: $('#password').val()}, function(msg) {
            if (msg.state) {
                $('.myContent').html('');
            } else {
                $.alert(msg.info);
            }
        }, 'json');
    });

    //全局控制用户跳转链接是否设置了交易密码
    $(".overallPsd").on("click", function() {
        var url = $(this).data('url');
        $.post("/site/ajax-overall-psd", {url:url}, function(msg) {
            if (msg.state) {
                window.location.href = msg.info;
            } else {
                $('.myContent').append(msg.info);
            }
        }, 'json');
    });

    //全局控制用户跳转链接是否设置了交易密码_关闭窗口
    $(".myContent").on("click", '.box-close', function() {
        $('.myContent').html('');
    });
});
