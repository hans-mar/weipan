$(function() {
    //使用现金和体验卷之间的切换
    $('#tabs').find('li').click(function() {
        $(this).addClass("active").siblings().removeClass("active");

        $(this).find(".img1").show();
        $(this).find(".img2").hide();

        $(this).siblings().find(".img2").show();
        $(this).siblings().find(".img1").hide();

        $('.tab-con').find('.tab-page').css('display', 'none');
        $('.tab-con').find('.tab-page').eq($(this).index()).css('display', 'block');

    });
    //数字累加累减
    $(".box").find(".plus").on("click", function() {
        //数字自加
        var $input = $(this).prev();
        var val = $input.val() * 1;
        var max = $input.data('max');
        if (max > 0) {
            if (val < max) {
                val += 1;
            }
        } else {
            if (val <= 40) {
                val = val + 10;
            }
        }

        //拿到展示的效果进行动画显示
        $(this).next().stop(true, true).text(val).animate({
            bottom: 35
        }, 400, function() {
            $(this).css("bottom", -35);
            $input.val(val);
        });
    });
    $(".box").find(".minus").on("click", function() {
        //数字自减
        var $input = $(this).next();
        var val = $input.val() * 1;
        var max = $input.data('max');
        if (max > 0) {
            if (val > 1) {
                val -= 1;
            }
        } else {
            if (val > 0) {
                val = val - 10;
            }
        }

        //拿到展示的效果进行动画显示
        $(this).nextAll().eq(2).text(val).animate({
            bottom: 35
        }, 400, function() {
            $(this).css("bottom", -35);
            $input.val(val);
        });
    });

    //花费切换
    $(".price-btn a").click(function() {
        $(this).addClass("price-bg").siblings().removeClass("price-bg");
        $('.productProfit').html($(this).data('profit'));
        $('.productFee').html($(this).data('fee'));
        var hand = $(this).data('max');
        $('.hand').attr('data-max', hand);
        $('.max_hand').html('(范围：1-'+hand+')');
    });

    //体验卷的卷种选择
    $('.display_in').click(function() {
        $('.co_on').removeClass('co_on');
        $(this).find('font').addClass('co_on');
        $(this).find(".img3").show();
        $(this).find(".img4").hide();
        //优惠劵张数的切换
        $('.numberCoupon').html($(this).find('.numberVolume').data('number'));
        $(this).siblings().find(".img4").show();
        $(this).siblings().find(".img3").hide();
    });

 
});