$(function () {
    var container = '.linkage-container';
    var dragOptions = {
        handle: ".linkage-drag-handle",
        onEnd: function (event) {
            var $ul = $(this.el),
                list = [];
            if (typeof event.newIndex != 'undefined' && event.newIndex != event.oldIndex) {
                $ul.children('li').each(function () {
                    list.push($(this).find(':first-child').data('key'));
                });
                $.post(getUrl($ul, 'sort-linkage-item'), {
                    params: $ul.parents(container).find(".linkageParams").val(),
                    list: list
                });
            }
        }
    };
    var getUrl = function ($this, url) {
        return $this.parents(container).find('.url-' + url).val();
    }

    $(container).coffee({
        click: {
            // checkbox全选
            '[name="selection_all"]': function () {
                $(this).parents(container).find('[name="selection[]"]').prop('checked', $(this).prop('checked'));
            },
            // 行收缩、伸展事件
            '.linkage-minus, .linkage-plus': function (event) {
                var $this = $(this);

                if ($this.attr('class').indexOf('linkage-minus') !== -1) {
                    // 收起
                    $this.addClass('linkage-plus');
                    $this.removeClass('linkage-minus');
                    $this.parent('p').parent('div').parent('li').children(':first-child').next().hide();
                } else {
                    // 展开
                    $this.removeClass('linkage-plus');
                    $this.addClass('linkage-minus');
                    $this.parent('p').parent('div').parent('li').children(':first-child').next().show();
                }
            },
            // 输入框型的点击修改事件
            '[data-action="textUpdate"]': function () {
                var $this = $(this);
                if ($this.find('input').length === 0) {
                    var $input = $('<input>').data('oldValue', $this.html()).val($this.html()).keyup(function (event) {
                        var key = $.getEventKey(event);
                        if (key === $.keyCode['ENTER']) {
                            $(this).trigger('blur');
                        }
                    });
                    $this.html($input);
                    $input.focus();
                }
            },
            // 下拉框型的点击修改事件
            '[data-action="selectUpdate"]': function () {
                var $this = $(this);
                if ($this.find('select').length === 0) {
                    var index = $this.index(),
                        $header = $this.parents(container).find('.linkage-header').find(':nth-child(' + (index + 1) + ')'),
                        items = $header.data('items'),
                        $select = $('<select>').data('oldValue', $this.html()),
                        $option;
                    $('<option>').val('').text(items['']).attr({'disabled': 'disabled', 'selected': 'selected'}).appendTo($select);
                    for (var key in items) {
                        if (key !== '') {
                            $option = $('<option>').val(key).text(items[key]);
                            if ($this.html() == items[key]) {
                                $select.find('option:selected').removeAttr('selected');
                                $option.attr('selected', 'selected');
                            }
                            $option.appendTo($select);
                        }
                    }
                    $this.html($select);
                }
            },
            // 添加元素按钮事件
            '.linkage-add-link': function () {
                if ($(".linkage-add-bar").length > 0) {
                    $(".linkage-add-bar").remove();
                    $(".linkage-selected").removeClass('linkage-selected');
                }
                $(this).addClass('linkage-selected');
                var $this = $(this),
                    $col = $this.parent(),
                    $row = $col.parent(),
                    rowTag = $row[0].localName,
                    colTag = $row.find('[data-field]:last')[0].localName,
                    $addItemBar = $("<" + rowTag + ">").addClass('linkage-add-bar'),
                    $form = $("<form>").attr({
                        'id': 'addLinkageForm',
                        'action': getUrl($this, 'add-linkage-item'),
                        'method': 'post'
                    });
                $row.find('[data-field]').each(function () {
                    var style = $(this).attr('style') || $(this).parent().attr('style');
                    $form.append(
                        $("<" + colTag + ">")
                            .attr('style', style)
                            .append(
                                $("<input>")
                                .attr('name', 'Linkage[' + $(this).data('field') + ']')));
                });
                var $addLink = $("<a>").addClass('addItemLink').data('pid', $this.data('pid')).html('添加'),
                    $cancelLink = $("<a>").addClass('cancelItemLink').html('取消'),
                    $buttonCol = $("<" + colTag + ">").attr('style', $col.attr('style'));
                $buttonCol.append($addLink).append('&nbsp;&nbsp;').append($cancelLink);
                $form.append($buttonCol);
                $addItemBar.append($form);
                $row.after($addItemBar);
                return false;
            },
            // 删除事件
            '.linkage-delete-link': function () {
                var $this = $(this),
                    $col = $this.parent(),
                    $row = $col.parent(),
                    title = $this.parents(container).find('.linkage-header').find(':first-child').html(),
                    value = $row.find(':first-child>[data-field]').html();
                $.confirm('确认删除 ' + title + ' 为 "' + value + '" 的这一项及其子项？', function () {
                    $this.parents(container).find('.linkage-action-tip').html('正在删除...').addClass('show');
                    $.post(getUrl($this, 'delete-linkage-item'), {
                        params: $row.parents(container).find(".linkageParams").val(),
                        key: $this.data('key')
                    }, function (msg) {
                        $this.parents(container).find('.linkage-action-tip').removeClass('show');
                        if (msg.state) {
                            var $li = $row.parent(),
                                $ul = $li.parent();
                            if ($ul.children().length === 1) {
                                $ul.prev().find('.linkage-minus').addClass('linkage-arrow').removeClass('.linkage-minus');
                                $ul.remove();
                            } else {
                                $li.remove();
                            }
                        } else {
                            $.alert(msg.info);
                        }
                    }, 'json');
                });
                return false;
            },
            // 添加元素事件
            '.addItemLink': function () {
                var $this = $(this);
                // 加载提示
                $this.parents(container).find('.linkage-action-tip').html('正在添加...').addClass('show');
                // 提交数据
                $("#addLinkageForm").ajaxSubmit($.config('ajaxSubmit', {
                    data: {
                        params: $this.parents(container).find(".linkageParams").val(),
                        pid: $this.data('pid')
                    },
                    success: function (msg) {
                        // 取消加载提示
                        $this.parents(container).find('.linkage-action-tip').removeClass('show');
                        if (msg.state) {
                            var data = msg.data.data,
                                key = msg.data.key,
                                $newRow = $this.parents('.linkage-add-bar').prev().clone(true),
                                isChildRow = $this.data('pid') != $newRow.data('pid') || false; // 判断点击添加的元素是同辈还是子类
                            // 为每行重新赋值
                            $newRow.children().each(function (index) {
                                if (index === 0) {
                                    // 第一行的特殊处理
                                    var $col = $(this).children('[data-field]');
                                    if (isChildRow) {
                                        if ($(this).children('.linkage-fork').length === 0) {
                                            $(this).children(':first-child').addClass('linkage-fork').attr('style', 'margin-left:0em;');
                                        } else {
                                            var style = $(this).children('.linkage-fork').attr('style');
                                                marginLeft = parseFloat(/margin-left:([0-9\.]+)em/.exec(style)[1]) + 1.5;
                                            style = style.replace(/margin-left:([0-9\.]+)em/,'margin-left:' + marginLeft.toString() + 'em');
                                            $(this).children('.linkage-fork').attr('style', style);
                                        }
                                    }
                                    $(this).children('.linkage-minus').addClass('linkage-arrow').removeClass('linkage-minus');
                                } else if ($(this).find('.linkage-add-link').length > 0 || $(this).find('.linkage-delete-link').length > 0) {
                                    // 操作栏的特殊处理
                                    $(this).find('.linkage-add-link').each(function () {
                                        // 如果当前按钮的 pid 值和当前行的 pid 值相同，表示当前按钮是添加同辈按钮
                                        if ($(this).data('pid') == $newRow.data('pid')) {
                                            if (isChildRow) {
                                                $(this).data('pid', $newRow.data('key'));
                                            } else {
                                                $(this).data('pid', $newRow.data('pid'));
                                            }
                                        } else {
                                            $(this).data('pid', key);
                                        }
                                    });
                                    $(this).find('.linkage-delete-link').data('key', key);
                                    return true;
                                } else {
                                    var $col = $(this);
                                }
                                if ($(this).attr('data-action') !== 'toggleUpdate') {
                                    $col.html(data[$col.data('field')]);
                                } else {
                                    $col.html($("<span>").addClass('linkage-yes'));
                                }
                            });
                            // 更改当前行的 key 和 pid
                            $newRow.data({
                                'key': key,
                                'pid': $this.data('pid')
                            }).attr({
                                'data-key': key,
                                'data-pid': $this.data('pid')
                            });
                            // 获取添加元素的父类
                            if ($this.parents(container).find('[data-key="' + $this.data('pid') + '"]').length == 0) {
                                $this.parents(container).children('ul').append($("<li>").append($newRow));
                            } else {
                                var $parentLi = $this.parents(container).find('[data-key="' + $this.data('pid') + '"]').parent('li');
                                // 根据该父类是否已经存在元素，来添加当前元素
                                if ($parentLi.children('ul').length === 0) {
                                    $parentLi.find('.linkage-arrow').addClass('linkage-minus').removeClass('linkage-arrow');
                                    $parentLi.append($("<ul>").addClass('linkage-ul').append($("<li>").append($newRow)));
                                    // 为新创建的 ul 元素绑定拖拽事件
                                    if (typeof Sortable !== 'undefined') {
                                        $parentLi.find('ul').dragSort($.config('dragSort', dragOptions));
                                    }
                                } else {
                                    $parentLi.children('ul').append($("<li>").append($newRow));
                                }
                            }
                            // 去除添加按钮的点击色
                            $(".linkage-selected").removeClass('linkage-selected');
                            // 移除添加栏
                            $this.parents('.linkage-add-bar').remove();
                        } else {
                            $.alert(msg.info);
                        }
                    }
                }));
            },
            // 取消添加按钮事件
            '.cancelItemLink': function () {
                $(".linkage-add-bar").remove();
                $(".linkage-selected").removeClass('linkage-selected');
            },
            // 状态栏切换事件
            '[data-action="toggleUpdate"] > span': function () {
                var $this = $(this),
                    $col = $this.parent(),
                    $row = $col.parent();

                if ($this.hasClass('linkage-yes')) {
                    var value = -1,
                        newClass = 'linkage-no',
                        oldClass = 'linkage-yes';
                } else {
                    var value = 1,
                        newClass = 'linkage-yes',
                        oldClass = 'linkage-no';
                }
                $.post(getUrl($this, 'toggle-linkage-item'), {
                    'params[params]': $this.parents(container).find(".linkageParams").val(),
                    'params[field]': $col.data('field'),
                    'params[key]': $row.data('key'),
                    'params[value]': value,
                }, function (msg) {
                    if (msg.state) {
                        $this.removeClass(oldClass).addClass(newClass);
                    } else {
                        $.alert(msg.info);
                    }
                }, 'json');
            }
        },
        blur: {
            // 输入框型的保存事件
            '[data-action="textUpdate"] > input': function (event) {
                var $input = $(this),
                    $col = $input.parent();
                    $row = $col.parent(),
                    value = $input.val();

                if (value === $input.data('oldValue')) {
                    $col.html(value);
                } else {
                    $.post(getUrl($input, 'ajax-update'), {
                        'params[model]': $input.parents(container).find(".linkageParams").val(),
                        'params[field]': $col.data('field'),
                        'params[key]': $row.data('key'),
                        'params[value]': value
                    }, function (msg) {
                        if (msg.state) {
                            $col.html(value);
                        } else {
                            $.alert(msg.info, function () {
                                $col.html($input.data('oldValue'));
                            });
                        }
                    }, 'json');
                }
            }
        },
        change: {
            // 下拉框型的保存事件
            '[data-action="selectUpdate"] > select': function () {
                var $select = $(this),
                    $col = $select.parent(),
                    $row = $col.parent(),
                    value = $select.val();

                $.post(getUrl($select, 'ajax-update'), {
                    'params[model]': $select.parents(container).find(".linkageParams").val(),
                    'params[field]': $col.data('field'),
                    'params[key]': $row.data('key'),
                    'params[value]': value
                }, function (msg) {
                    if (msg.state) {
                        $col.html($select.find('option:selected').text());
                    } else {
                        $.alert(msg.info, function () {
                            $col.html($select.data('oldValue'));
                        });
                    }
                }, 'json');
            }
        }
    });

    // 初始化
    (function () {
        // 遍历所有列表
        $(container).each(function () {
            // 获得总列数
            var length = $(this).find('.linkage-header > p').length;
            // 如果不存在数据则直接跳过
            if (length <= 0) {
                return true;
            }
            // 初始化存储各列最大宽度的数组
            var widthMap = [];
            for (var i = 0; i < length; i++) {
                widthMap[i] = 0;
            }
            // 遍历所有的列，获得每列最大宽度
            $(this).find('ul > li > div > p').each(function () {
                var width = $(this).width();
                if (widthMap[$(this).index()] < width) {
                    widthMap[$(this).index()] = width;
                }
            });
            // 获得总列宽
            var sum = widthMap.reduce(function (a, b) {
                return a + b;
            });
            // 每列该增加的宽度
            var avg = ($(this).find('.linkage-header').width() - sum) / length;
            // 标题增加列宽
            $(this).find('.linkage-header > p').each(function () {
                $(this).width(widthMap[$(this).index()] + avg); 
            });
            // 各行增加列宽
            $(this).find('ul > li > div > p').each(function () {
                $(this).width(widthMap[$(this).index()] + avg); 
            });
        });
    })();

    // 拖拽事件绑定
    if (typeof Sortable !== 'undefined') {
        $(container + " ul").dragSort($.config('dragSort', dragOptions));
    }
});