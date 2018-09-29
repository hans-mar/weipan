$.fn.getTable = function (dataSource, columns, options) {
    /**
     * 初始化列设置
     */
    columns = (function (columns) {
        var tmpArr = [];
        for (var key in columns) {
            if (typeof columns[key] !== 'object') {
                columns[key] = {'header': String(columns[key])};
            }
            columns[key]['key'] = key;
            tmpArr.push(columns[key]);
        }
        return tmpArr;
    })(columns);
    /**
     * 初始化配置项
     */
    var paging = options.paging === false ? false : parseInt(options.paging),
        defaults = {
            info: true,
            // 文字信息调整
            language: {
                search: '搜索',
                searchPlaceholder: '',
                zeroRecords: '没有找到符合条件的数据',
                info: '当前显示 _START_-_END_ 项，共 _TOTAL_ 项',
                infoEmpty: '显示第 0 至 0 项结果，共 0 项',
                infoFiltered: '（从 _MAX_ 条记录中过滤）',
                lengthMenu: '每页显示_MENU_条',
                loadingRecords: 'Now Loading...',
                // processing: '<img src="/images/loading.gif" style="position:relative; z-index:1000" height="24">',
                paginate: {
                    first: "首页",
                    previous:'前一页',
                    next:'后一页',
                    last:'尾页'
                }
            },
            lengthMenu: [[paging, paging * 2, paging * 5, -1], [paging, paging * 2, paging * 5, "All"]],
            // 是否开启分页
            paging: paging,
            // 每页条数
            displayLength: paging,
            // 分页页码样式
            pagingType: 'full_numbers',
            // 是否显示载入进度
            processing: true,
            // 是否开启服务器端
            serverSide: false,
            //是否出现X轴滚动轴
            scrollX: false,
            //延迟渲染，以提高datatables的运行速度，大数据量时使用
            deferRender: true

            // 表格高
            // scrollY: 500

            // 和scrollY搭配可实现整个表格数据在固定的高度下全部展示
            // scrollCollapse: true
            
            //每次重新绘制表格的回调
            //drawCallback: function () {}
            
            //表格载入完毕后的回调
            //initComplete: function () {}
        };
    // 数据源初始化
    if (typeof dataSource === 'string') {
        defaults.ajax = {
            url: dataSource,
            dataSrc: function (msg) {
                if (msg.state) {
                    return msg.info;
                } else {
                    return [];
                }
            },
            type: 'post'
        };
        defaults.columns = getColumns();
    } else {
        defaults.data = dataSource;
        defaults.columns = getColumns();
    }
    // 单元格渲染
    defaults.columnDefs = (function () {
        return columns.map(function (item, index) {
            return {
                targets: index,
                orderable: item.sort === false ? false : true,
                searchable: item.search || false,
                visible: item.visible === false ? false : true,
                render: function (data, type, row) {
                    if (item.value) {
                        if (typeof item.value === 'function') {
                            // 操作栏的回调方法
                            if (item.key === '') {
                                return item.value(row);
                            } else {
                                return item.value(data, row);
                            }
                        } else {
                            return item.value;
                        }
                    } else {
                        return data;
                    }
                }
            };
        });
    })();
    // 行回调方法
    defaults.rowCallback = function (row, data, index) {
        // 设置行属性
        var rowOptions = options.rowOptions;
        if (rowOptions) {
            if (typeof rowOptions === 'function') {
                $(row).attr(rowOptions(data, index));
            } else {
                $(row).attr(rowOptions);
            }
        }
        // 设置列属性
        for (var i = 0, length = columns.length; i < length; i++) {
            if (columns[i].options) {
                if (typeof columns[i].options === 'function') {
                    $(row).find('td:eq(' + i + ')').attr(columns[i].options(data, index));
                } else {
                    $(row).find('td:eq(' + i + ')').attr(columns[i].options);
                }
            }
        }
    };

    /**
     * 获取列设置
     */
    function getColumns() {
        return columns.map(function (item) {
            return {
                data: item.key,
                title: item.header,
                orderSequence: ['desc', 'asc']
            };
        });
    }

    // 判断是否是中文，是中文则采用中文排序
    $.fn.dataTable.ext.type.detect.unshift(function (word) {
        return /^[\u4E00-\u9FA5]+$/.test(word) ? 'chinese' : null;
    });

    $.fn.dataTableExt.type.order['chinese-asc']  = function(x, y) {
        return x.localeCompare(y);
    };

    $.fn.dataTableExt.type.order['chinese-desc']  = function(x, y) {
        return y.localeCompare(x);
    };

    return this.DataTable($.extend(true, defaults, options));
};