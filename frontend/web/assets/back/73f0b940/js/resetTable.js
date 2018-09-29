$(function () {
    // 默认未初始化过
    var isInited = false;
    // 左侧栏目点击事件
    $('.left-content').on('click', 'p', function () {
        var $this = $(this);

        if (getSelectedColumnNum() > getMaxColumnNum()) {
            $.alert('选择的列数已经达到上限啦~！');
            return false;
        }

        var $p = $('<p>');
        $('<span>').html($this.html()).appendTo($p);
        $('<em>').addClass('remove').appendTo($p);
        $('<input>').attr({type: 'hidden', name: 'field[]'}).val($this.attr('field')).appendTo($p);
        $('.right-content').append($p);

        $this.remove();
        updateSelectedNum();
    });

    // 右侧栏目删除事件
    $('.right-content').on('click', 'em.remove', function () {
        var $this = $(this);
        var $parent = $this.parent('p');

        var $p = $('<p>').attr('field', $parent.find('input').val()).html($parent.find('span').html());
        $('.left-content').append($p);

        $this.parent('p').remove();
        updateSelectedNum();
    });

    // 重置按钮
    $('.reset-button').click(function () {
        if (isInited) {
            $.alert('您还没为该表格设置过~！所以无法重置~！！');
            return false;
        }
        $.confirm('确认重置该表格的列？', function () {
            $('[name="isReset"]').val('1');
            $('#resetTableForm').ajaxSubmit($.config('ajaxSubmit', {
                success: function () {
                    window.parent.document.location.reload();
                }
            }));
        });
    });

    // 确认按钮
    $('.submit-button').click(function () {
        var atLeastNum = 3;
        if (getSelectedColumnNum() < atLeastNum) {
            alert('至少选择 ' + atLeastNum + ' 列~！');
            return false;
        }
        $('#resetTableForm').ajaxSubmit($.config('ajaxSubmit', {
            success: function () {
                window.parent.document.location.reload();
            }
        }));
    });

    // 获取已经选择的列数
    var getSelectedColumnNum = function () {
        return $('.right-content>p').length;
    };

    // 获取允许的最大列数
    var getMaxColumnNum = function () {
        return $('#maxColumnNum').val();
    }

    // 更新已选择栏位数
    var updateSelectedNum = function () {
        $("#selectedCount").html(getSelectedColumnNum());
    }

    // 如果有选择了，则初始化选择项
    if ($('#setFieldList').val()) {
        var pieces = $('#setFieldList').val().split(',');
        for (var k in pieces) {
            $('p[field="' + pieces[k] + '"]').click();
        }
    } else {
        // 如果还未选择过，则初始化显示状态
        isInited = true;
        for (var i = 0; i < getMaxColumnNum(); i++) {
            $('.left-content p:eq(0)').click();
        }
    }
});