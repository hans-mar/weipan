$(function() {
    var paginate = function () {
        $("#migration-history-table").paginate({'limit': 10, 'columns': [1, 3, 4]});
    };

    $("#ajax-area").coffee({
        click: {
            // 删除一个迁移记录事件
            '.delete-migration': function () {
                var $a = $(this);
                $.confirm('确认删除 "' + $a.parent().data('desc') + '"？', function () {
                    $.post($a.attr('href'), {}, function (msg) {
                        if (msg.state) {
                            $.alert(msg.info, function () {
                                $a.parents('tr').remove();
                            });
                        } else {
                            $.alert(msg.info);
                        }
                    }, 'json');
                });
                return false;
            },
            // 同步一条迁移记录事件
            '.sync-migration': function () {
                var $a = $(this);
                $.confirm('确认同步 "' + $a.parent().data('desc') + '"？', function () {
                    $.post($a.attr('href'), {}, function (msg) {
                        if (msg.state) {
                            $a.parent().html(msg.info);
                        } else {
                            $.alert('同步失败：\n' + msg.info);
                        }
                    }, 'json');
                });
                return false;
            },
        }
    });

    // 创建迁移提交按钮事件
    $("body").on('click', '.migrateSubmit', function () {
        $("#migrateForm").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (msg.state) {
                    $.alert(msg.info, function () {
                        if (msg.data && msg.data.isEdit) {
                            $.fancybox.close();
                        } else {
                            location.href = 'history-list';
                        }
                    });
                } else {
                    $.alert(msg.info);
                }
            }
        }));
    });

    // 同步所有迁移记录事件
    $("#sync-all").click(function () {
        var $a = $(this);
        $.confirm('确认同步所有版本么？', function () {
            $.post($a.attr('href'), {action: 'generateMigrate'}, function (msg) {
                $.alert(msg.info, function () {
                    if (msg.state) {
                        location.reload();
                    }
                });
            }, 'json');
        });
        return false;
    });

    // 搜索输入框回车事件
    $("#searchInput").keyup(function (event) {
        var key = $.getEventKey(event);
        if (key === $.keyCode['ENTER']) {
            $("#searchSubmit").trigger('click');
            return false;
        }
    });

    // 搜索事件
    $("#searchSubmit").click(function () {
        $.get('', {key: $("#searchInput").val()}, function (msg) {
            $("#ajax-area").html(msg);
            paginate();
        }, 'html');
    });

    'view-fancybox'.config({
        'minWidth': 600
    });

    paginate();
});