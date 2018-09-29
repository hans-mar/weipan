$(function () {
    var container = '.list-container';
    var refreshView = '.list-view';

    $(container).coffee({
        click: {
            // checkbox全选
            '[name="selection_all"]': function () {
                $('[name="selection[]"]').prop('checked', $(this).prop('checked'));
            },
            // 分页和排序的Ajax形式
            'ul.pagination a, [data-sort]': function () {
                var $this = $(this);
                if (isAjax($this)) {
                    showLoadingImg($this);
                    $.get($this.attr('href'), function (msg) {
                        hideLoadingImg($this);
                        $this.parents(refreshView).html(msg.info);
                    }, 'json');

                    return false;
                }
            },
            // 搜索按钮的Ajax形式
            '.search .submit-input': function () {
                var $this = $(this),
                    $form = $this.parents('form.search-form');
                if (isAjax($this)) {
                    showLoadingImg($this);
                    $form.find('[name="exportExcel"]').remove();
                    $form.ajaxSubmit($.config('ajaxSubmit', {
                        success: function (msg) {
                            hideLoadingImg($this);
                            $this.parents(container).find(refreshView).html(msg.info);
                            for (var key in msg.data) {
                                $('.' + key).html(msg.data[key]);
                            }
                        }
                    }));

                    return false;
                }
            },
            // Excel导出按钮事件
            '.exportBtn': function () {
                var $searchForm = $(this).parents(container).find('form.search-form');
                    $searchSubmit = $searchForm.find('.search .submit-input');
                if ($searchForm.length > 0) {
                    if ($searchForm.find('.exportExcel').length === 0) {
                        $searchForm.append($("<input>").attr({
                            type: 'hidden',
                            name: 'exportExcel',
                            value: 1
                        }));
                    }
                    $searchForm.submit();
                    return false;
                }
            },
            // 删除按钮事件
            'a.deleteLink': function () {
                var $this = $(this);
                if (isAjax($this)) {
                    $.confirm("确认删除？", function () {
                        $.post($this.attr('href'), {
                            id: $this.data('key'),
                            model: $this.data('model')
                        }, function (msg) {
                            if (msg.state) {
                                if (msg.info) {
                                    $.alert(msg.info, function () {
                                        $this.parents('tr').remove();
                                    });
                                } else {
                                    $this.parents('tr').remove();
                                }
                            } else {
                                $.alert(msg.info);
                            }
                        }, 'json');
                    })

                    return false;
                }
            },
            // 表格单字段输入框型的点击修改事件
            'td[data-action="textUpdate"]': function () {
                var $td = $(this);
                if ($td.find('input').length === 0) {
                    var $input = $('<input>').data('oldValue', $td.html()).val($td.html()).keyup(function (event) {
                        var key = $.getEventKey(event);
                        if (key === $.keyCode['ENTER']) {
                            $(this).trigger('blur');
                        }
                    });
                    $td.html($input);
                    $input.focus().select();
                } else {
                    if (!$td.find('input').data('oldValue')) {
                        $td.find('input').data({
                            'oldValue': $td.find('input').val(),
                            'noRevert': true
                        });
                    }
                }
            },
            // 表格单字段下拉框型的点击修改事件
            'td[data-action="selectUpdate"]': function () {
                var $td = $(this);
                if ($td.find('select').length === 0) {
                    var index = $td.parents('tr').find('td').index($td),
                        $tr = $td.parents('table').find('thead>tr>th:eq(' + index + ')'),
                        items = $tr.data('items'),
                        $select = $('<select>').data('oldValue', $td.html()),
                        $option;
                    if (items[''] !== undefined) {
                        $('<option>').val('').text(items['']).attr({'disabled': 'disabled', 'selected': 'selected'}).appendTo($select);
                    }
                    for (var key in items) {
                        if (key !== '') {
                            $option = $('<option>').val(key).text(items[key]);
                            if ($td.html() == items[key]) {
                                $select.find('option:selected').removeAttr('selected');
                                $option.attr('selected', 'selected');
                            }
                            $option.appendTo($select);
                        }
                    }
                    $td.html($select);
                }
            },
            // 批量删除按钮
            'a.deleteAllBtn': function () {
                var list = [],
                    $this = $(this);
                $this.parents(container).find("table input[name='selection[]']:checked").each(function () {
                    list.push($(this).val());
                });
                if (list.length === 0) {
                    $.alert('至少选择一个');
                    return false;
                }
                $.confirm('确认删除所选？', function () {
                    $.post($this.attr('href'), {list: list, model: $this.data('model')}, function (msg) {
                        if (msg.state) {
                            $.alert(msg.info || '批量删除成功', function () {
                                window.location.reload();
                            });
                        } else {
                            $.alert(msg.info);
                        }
                    }, 'json');
                });
                return false;
            }
        },
        blur: {
            // 表格单字段输入框型的保存事件
            'td[data-action="textUpdate"] > input': function () {
                var $input = $(this),
                    $td = $input.parent('td');
                    value = $input.val();

                if (!$td.data('key')) {
                    $td.html('');
                    return false;
                }

                if (value === $input.data('oldValue')) {
                    if (!$td.find('input').data('noRevert')) {
                        $td.html(value);
                    }
                } else {
                    $.post($td.attr('href'), {
                        'params[field]': $td.data('field'),
                        'params[model]': $td.data('model'),
                        'params[key]': $td.data('key'),
                        'params[value]': value
                    }, function (msg) {
                        if (msg.state) {
                            if (!$td.find('input').data('noRevert')) {
                                $td.html(value);
                            } else {
                                $td.find('input').data('oldValue', value);
                            }
                        } else {
                            $.alert(msg.info, function () {
                                if (!$td.find('input').data('noRevert')) {
                                    $td.html($input.data('oldValue'));
                                }
                            });
                        }
                    }, 'json');
                }
            }
        },
        change: {
            // 表格单字段下拉型的保存事件
            'td[data-action="selectUpdate"] > select': function () {
                var $select = $(this),
                    $td = $select.parent('td'),
                    value = $select.val();

                $.post($td.attr('href'), {
                    'params[field]': $td.data('field'),
                    'params[model]': $td.data('model'),
                    'params[key]': $td.data('key'),
                    'params[value]': value
                }, function (msg) {
                    if (msg.state) {
                        $td.html($select.find('option:selected').text());
                    } else {
                        $.alert(msg.info, function () {
                            $td.html($select.data('oldValue'));
                        });
                    }
                }, 'json');
            }
        }
    });

    var isAjax = function ($obj) {
        return $obj.parents(container).find('input.isAjax').val() === '1';
    };

    var showLoadingImg = function ($obj) {
        $obj.parents(container).find('.search .submit-input').next().attr('style', 'display: inline-block;');
    };

    var hideLoadingImg = function ($obj) {
        $obj.parents(container).find('.search .submit-input').next().hide();
    };
});