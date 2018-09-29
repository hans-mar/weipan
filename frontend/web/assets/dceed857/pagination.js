$(function () {
    $.fn.extend({
        /**
         * js表格分页、排序方法，使用例子：
         * ```js
         * $(".table").paginate();
         * ```
         * 
         * @param  integer|json options 配置项，如果只传入数字，表示是每页个数，其他参数项如下
         * -[sort]        boolean    是否开启排序
         * -[pager]       boolean    是否开启分页
         * -[limit]       integer    分页每页显示行数
         * -[pageParam]   string     分页参数名
         * -[pageNum]     integer    最大页码个数
         * -[columns]     array|json 指定要排序的列，eq:[1,3]，表示第1、3列进行排序，或者:{1:int,3:float}，指定对应列采用指定方式排序
         * -[sortOptions] json       其他排序参数
         */
        paginate: function (options) {
            var Sort = function ($table, sortOptions) {
                sortOptions = sortOptions || {};
                // 配置参数初始化
                var columns = sortOptions.columns || [],
                    limit = sortOptions.limit || 10,
                    options = sortOptions.sortOptions || {},
                    isPager = sortOptions.pager !== false,
                    headIndex = sortOptions.head && sortOptions.head - 1 || 0;

                // 其他参数配置
                var eventClassName = 'sort-table-th';

                // 定义内部全局变量
                var $firstTr, firstSort, thTemplate, thJson = {};

                this.run = function () {
                    // 内部参数初始化
                    init();
                    // 设置列排序属性
                    setColumnSortOptions();
                    // 重置首行
                    resetTh();
                    // 绑定事件
                    bindEvent();
                };

                var init = function () {
                    // 标题模板
                    thTemplate = '<a sort="{{sort}}" colNum="{{colNum}}" class="' + eventClassName + '" href="javascript:;">{{title}}</a>';
                    // 获取首行
                    $firstTr = $table.find('tr:eq(' + headIndex + ')');
                    // 是否首次排序
                    firstSort = options.sort || 'desc';
                    if ($table.children('thead').length === 0) {
                        $table.prepend($("<thead>").append($firstTr));
                    }
                };

                var setColumnSortOptions = function () {
                    var length,
                        col,
                        i;
                    
                    if ($.isArray(columns)) {
                        length = columns.length;
                        if (length > 0) {
                            for (i = 0; i < length; i++) {
                                thJson[columns[i]] = {};
                                thJson[columns[i]]['$'] = $firstTr.find(':eq(' + (columns[i] - 1) + ')');
                                thJson[columns[i]]['type'] = 'auto';
                            }
                        } else {
                            length = $firstTr.children().length;
                            for (i = 0; i < length; i++) {
                                thJson[i + 1] = {};
                                thJson[i + 1]['$'] = $firstTr.find(':eq(' + i + ')');
                                thJson[i + 1]['type'] = 'auto';
                            }
                        }
                    } else {
                        for (col in columns) {
                            thJson[col] = {};
                            thJson[col]['$'] = $firstTr.find(':eq(' + (col - 1) + ')');
                            thJson[col]['type'] = columns[col];
                        }
                    }
                };

                var resetTh = function () {
                    var key, title, sort, colNum, thHtml;
                    for (key in thJson) {
                        title = thJson[key]['$'].html();
                        sort = 'none';
                        colNum = key;
                        thHtml = thTemplate.replace(/{{sort}}|{{title}}|{{colNum}}/g, function (word) {
                            switch (word) {
                                case '{{sort}}': return sort;
                                case '{{title}}': return title;
                                case '{{colNum}}': return colNum;
                            }
                        });
                        thJson[key]['$'].html(thHtml);
                    }
                };

                var bindEvent = function () {
                    // 绑定点击排序的事件
                    $table.on('click', 'a.' + eventClassName, function () {
                        // 当前点击链接的jQuery对象
                        var $a = $(this);
                        // 获取当前被点击排序的表格的tbody
                        var $theTable = $a.parents('table:first')
                        var $tbody = $theTable.find('tbody');
                        // 获取当前点击按钮，是整个表格的第几列
                        var colNum = $a.attr('colNum');
                        // 先获取当前的排序状态
                        var nowSort = $a.attr('sort');
                        var nextSort, sortCallback, sortFunc, newTrList;
                        switch (nowSort) {
                            case 'desc':
                                nextSort = 'asc';
                                break;
                            case 'asc':
                                nextSort = 'desc';
                                break;
                            default:
                                nextSort = firstSort;
                        }
                        // 每次改变都会重置其他列的排序状态
                        $theTable.find('a.' + eventClassName).attr('sort', 'none');
                        // 然后改变当前点击列的图片的排序显示
                        $a.attr('sort', nextSort);
                        // 定义数据类型转换方法
                        var convert = function ($tr) {
                            var $td = $tr.find('td:eq(' + (colNum - 1) + ')');
                            var value = $td.find(':last').html() || $td.html();

                            var convertInt = function (value) {
                                return parseInt(value);
                            };
                            var convertFloat = function (value) {
                                return parseFloat(value);
                            };
                            var convertDate = function (value) {
                                return parseInt(Date.parse(value)) / 10000;
                            };
                            var convertCurrency = function (value) {
                                return parseInt(value.replace(/,/g, '').replace(/^([^\d\.]+)([\d\.]+)/g,'$2'));
                            };
                            var convertQuantity = function (value) {
                                return parseInt(value.replace(/[,\.]/g, ''));
                            };
                            var convertDefault = function (value) {
                                return value.toString();
                            };

                            switch (thJson[colNum]['type']) {
                                case 'auto':
                                    if (!isNaN(value)) {
                                        if (value.indexOf('.') === -1) {
                                            return convertInt(value);
                                        } else {
                                            return convertFloat(value);
                                        }
                                    } else {
                                        if (value.indexOf('-') !== -1 || value.indexOf(':') !== -1) {
                                            return convertDate(value);
                                        } else if (!isNaN(value.substr(0,1)) && value.indexOf(',') !== -1) {
                                            return convertQuantity(value);
                                        } else if (isNaN(value.substr(0,1)) && /\d/.exec(value) !== null) {
                                            return convertCurrency(value);
                                        } else {
                                            return convertDefault(value);
                                        }
                                    }
                                case 'int':
                                    return convertInt(value);
                                case 'float':
                                    return convertFloat(value);
                                case 'date':
                                    return convertDate(value);
                                case 'currency':
                                    return convertCurrency(value);
                                case 'quantity':
                                    return convertQuantity(value);
                                default:
                                    return convertDefault(value);
                            }
                        };
                        // 定义排序方法
                        var sortFunc = function (v1, v2, sortType) {
                            var val1 = convert(v1),
                                val2 = convert(v2),
                                ret;
                            if (val1 > val2) {
                                ret = 1;
                            } else if (val1 < val2) {
                                ret = -1;
                            } else {
                                ret = 0;
                            }
                            if (sortType === 'desc') {
                                ret *= -1;
                            }
                            return ret;
                        };
                        var sortAscFunc = function (v1, v2) {
                            return sortFunc(v1, v2, 'asc');
                        };
                        var sortDescFunc = function (v1, v2) {
                            return sortFunc(v1, v2, 'desc');
                        };
                        // 收集被排序的td,同时记录显示的表格个数
                        var trList = [];
                        $tbody.find('tr').each(function () {
                            trList.push($(this));
                        })
                        // 开始排序
                        if (nextSort === 'desc') {
                            sortCallback = sortDescFunc;
                        } else {
                            sortCallback = sortAscFunc;
                        }
                        // 首次排序，非首次直接反转
                        if ($a.attr('sorted') === '1') {
                            newTrList = trList.reverse();
                        } else {
                            // 每次对新的列排序时，将其他列的首次排序效果清空
                            $theTable.find('a.' + eventClassName).attr('sorted', 0);
                            newTrList = trList.sort(sortCallback);
                        }
                        // 将排序结果显示在页面
                        $tbody.children().remove();
                        for (var key in newTrList) {
                            $tbody.append(newTrList[key]);
                        }
                        // 排序完后增加标识，下次排序直接反转即可
                        $a.attr('sorted', 1);
                        if (isPager === true) {
                            var pager = new Pagination($theTable, sortOptions);
                            pager.run();
                        }
                    });
                }
            };
            var Pagination = function ($table, pagerOptions) {
                pagerOptions = !pagerOptions && {} || !isNaN(pagerOptions) && {limit: pagerOptions} || pagerOptions;
                // 配置参数初始化
                var limit = pagerOptions.limit || 10,
                    pageParam = pagerOptions.pageParam || 'page', // 分页参数的变量名
                    pageNum = pagerOptions.pageNum || 10, // 最多显示的页码数
                    headIndex = pagerOptions.head && pagerOptions.head - 1 || 0;
                // 其他参数初始化
                var $tbody = $table.children('tbody'),
                    pagerClass = 'pager',
                    href = document.location.href;
                // 定义内部全局变量
                var page, totalCount, totalPage;

                this.run = function () {
                    paginate();
                };

                var paginate = function (currentPage, isInputPage) {
                    // 当前页码初始化
                    page = (!currentPage || currentPage <= 0) && 1 || parseInt(currentPage);
                    // 参数初始化
                    init();
                    // 如果数据总数小于每页显示个数，则直接返回
                    if (limit === 0 || totalCount <= limit) {
                        return;
                    }
                    // 生成容器
                    var $pager = renderContainer();
                    // 生成文本区域
                    $pager = renderText($pager, isInputPage);
                    // 生成页码区域
                    $pager = renderPage($pager);
                    // 移除页面上已经存在的分页
                    $table.next("div." + pagerClass).remove();
                    // 在表格后添加当前分页
                    $table.after($pager);
                    // 事件绑定
                    bindEvent(isInputPage);
                };

                var init = function () {
                    // 获取首行
                    if ($table.children('thead').length === 0) {
                        var $firstTr = $table.find('tr:eq(' + headIndex + ')');
                        $table.prepend($("<thead>").append($firstTr));
                    }
                    totalCount = $tbody.children('tr').length;
                    // 总页码的计算
                    totalPage = Math.ceil(totalCount / limit);
                    // 当前页码校正
                    page = page >= totalPage && totalPage || page;
                    // 跳转链接初始化
                    href = href.indexOf(pageParam) !== -1 && delUrlParams(href, [pageParam]) || href;
                    // 设置被显示的内容的jQuery选择器
                    var selector = page === 1 && 'tr:lt(' + limit + ')' || 'tr:gt(' + ((page - 1) * limit - 1) + '):lt(' + limit + ')';
                    // 页面的初始化
                    $tbody.find('tr').hide();
                    $tbody.find(selector).show();
                };

                var bindEvent = function (isInputPage) {
                    // 判断输入框是否聚焦
                    if (isInputPage === true) {
                        $table.next("div." + pagerClass).find('.page-go').focus().select();
                    }
                    // 对页码的事件绑定
                    $table.next("div." + pagerClass).children('ul').on('click', 'a', function() {
                        var page = $(this).attr(pageParam);

                        paginate(page, false);

                        return false;
                    });
                    // 对跳转按钮的事件绑定
                    $table.next("div." + pagerClass).on('click', '.page-go-btn', function() {
                        var goPage = parseInt($(this).prev().val());

                        paginate(goPage, true);
                    });
                    // 对跳转输入框的回车事件绑定
                    $table.next("div." + pagerClass).on('keydown', '.page-go', function(e) {
                        var currKey = $.getEventKey(e);
                    
                        if (currKey == $.keyCode['ENTER']) {
                            var goPage = parseInt($(this).val());
                            paginate(goPage, true);
                            return false;
                        }
                    });
                };

                var renderContainer = function () {
                    return $("<div>").addClass(pagerClass);
                };

                var renderText = function ($container, isInputPage) {
                    return $container
                        .append('共 <span style="color:#f11">' + totalPage + '</span> 页')
                        .append($("<input>").attr({
                            'type': 'text',
                            'href': href,
                            'class': 'page-go',
                            'value': isInputPage === true && page || ''
                        }))
                        .append($("<input>").attr({
                            'type': 'button',
                            'class': 'page-go-btn',
                            'value': 'GO'
                        }));
                };

                var renderLi = function (text, page, liClass) {
                    var params = {};
                    params[pageParam] = page;
                    return $("<li>")
                        .attr('class', liClass)
                        .append($("<a>")
                            .attr({
                                'page': page,
                                'href': addUrlParams(href, params)
                            })
                            .html(text)
                        );
                };

                var renderPage = function ($container) {
                    // 页码参数初始化
                    var $ul = $("<ul>").attr('class', 'pagination'),
                        p = 1,
                        hiddenClass = page === p && ' hidden' || '',
                        showPageNum = totalPage > pageNum && pageNum || totalPage,
                        diffPage = parseInt((pageNum - 1) / 2),
                        endPage = diffPage + page > totalPage && totalPage || diffPage + page,
                        startPage = endPage - pageNum + 1;
                    // 添加首页
                    $ul.append(renderLi('首页', p, 'first' + hiddenClass));
                    p = page !== p && page - 1 || p;
                    // 添加上一页
                    $ul.append(renderLi('上一页', p, 'prev' + hiddenClass));
                    // 校正初始页码和结束页码的合法区间
                    if (startPage <= 0) {
                        startPage = 1;
                        endPage = startPage + pageNum - 1;
                    }
                    endPage = endPage > totalPage && totalPage || endPage;
                    // 添加页码
                    for (var i = startPage; i <= endPage; i++) {
                        var liSelected = page === i && ' active' || '';
                        $ul.append(renderLi(i, i, 'page' + liSelected));
                    }
                    p = page >= totalPage && totalPage || page + 1;
                    hiddenClass = page === p && ' hidden' || '';
                    // 添加下一页
                    $ul.append(renderLi('下一页', p, 'next' + hiddenClass));
                    // 添加末页
                    $ul.append(renderLi('末页', totalPage, 'last' + hiddenClass));

                    return $container.append($ul);
                };

                var addUrlParams = function (url, obj) {
                    var joinChar = (url.indexOf('?') === -1) ? '?' : '&',
                        arrParams = [];
                    for (var key in obj) {
                        arrParams.push(joinChar + key + '=' + obj[key]);
                        joinChar = '&';
                    }
                    var strParams = arrParams.join('');
                    return url + strParams;
                };

                var delUrlParams = function (url, obj) {
                    for (var key in obj) {
                        var reg = new RegExp('([\\?|&])' + obj[key] + '=(\\w*)&?', "i");
                        url = url.replace(reg, '$1');
                    }
                    // 如果末尾有&或?号则删除
                    var r = new RegExp('[\?|&]$', 'i');
                    url = url.replace(r, '');
                    return url;
                };
            };
            options = !options && {} || !isNaN(options) && {limit: options} || options;
            isPager = options.pager !== false;
            isSort = options.sort !== false;
            this.each(function () {
                if (isPager === true) {
                    var pager = new Pagination($(this), options);
                    pager.run();
                }
                if (isSort === true) {
                    var sort = new Sort($(this), options);
                    sort.run();
                }
            });
            return this;
        }
    });
});