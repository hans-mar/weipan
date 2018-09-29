$(function () {
    // 禁止绑定click事件
    $("#wizard-toolbar").coffee({
        change: {
            // 选择项目下拉框事件
            'select.applicationSelect': function () {
                 if ($(this).val() === '') {
                    $(this).hide().next().show().focus().on('keydown', function (event) {
                        if ($(this).val() === '' && $.getEventKey(event) === $.keyCode['BACKSPACE']) {
                            $(this).hide().off('keydown').prev().show().get(0).selectedIndex = 1;
                            return false;
                        }
                    });
                }
            }
        },
        mouseup: {
            '#wizardMoreBtn': function () {
                if ($(this).html() === '更多') {
                    $(".wizard-area-hidden").slideDown();
                    $(this).html('隐藏');
                } else {
                    $(".wizard-area-hidden").slideUp();
                    $(this).html('更多');
                }
            }
        },
        keydown: {
            // 输入框回车事件
            'input[type="text"]': function (event) {
                if ($(this).val() !== '' && $.getEventKey(event) === $.keyCode['ENTER']) {
                    $(this).parents('.wizard-area').find('.wizard-submit').trigger('click');
                }
            }
        },
        mouseover: {
            // 显示悬浮说明事件
            '.wizard-question': function () {
                $(this).next().css('display', 'inline');
            }
        },
        mouseleave: {
            // 关闭悬浮说明事件
            '.wizard-question': function () {
                $(this).next().hide();
            }
        }
    });
    // 按钮提交事件
    $(".wizard-submit").click(function () {
        $("#submitType").val($(this).data('action'));
        $("#wizardForm").ajaxSubmit($.config('ajaxSubmit'));
    });
    // 工具栏弹出事件
    $("#wizard-toolbar").hover(function () {
        $(this).css('left', '0px');
    });
    // 任意点击触发收缩工具栏事件
    $("body").on('click', function () {
        var flag = true;
        // 判断是否已经绑定过该元素的点击事件
        if (!$._data($("#wizard-toolbar")[0], 'events').click || $._data($("#wizard-toolbar")[0], 'events').click.length === 0) {
            flag = false;
            // 在工具栏内部点击阻止触发收缩事件
            $("#wizard-toolbar").on('click', function (event) {
                event.stopPropagation();
            });
        }
        if (flag === true) {
            $('#wizard-toolbar').css('left', '-79px');
        }
    });
    // 添加数据库迁移按钮的元素的冒泡阻止事件
    $("#create-migration").click(function (event) {
        $("#wizard-toolbar").off('click');
    });
    // 设置数据迁移弹窗宽度
    'iframe-fancybox'.config({
        'width': 1200,
        'minHeight': 500
    });
});