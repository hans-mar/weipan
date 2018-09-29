$(function () {
    // 扩展后台全局静态方法
    $.extend({
        /**
         * tab栏点击切换事件
         * 
         * @param  string tabBar tab选项卡
         * @param  string tabCon tab内容页
         */
        tab: function (tabBar, tabCon) {
            var tabBar = tabBar || '.tab-container .tabBar span',
                tabCon = tabCon || '.tab-container .tabCon',
                $tabMenu = $(tabBar),
                className = 'current',
                i = 0;
            // 初始化操作
            $tabMenu.removeClass(className);
            $(tabBar).eq(i).addClass(className);
            $(tabCon).hide();
            $(tabCon).eq(i).show();

            $tabMenu.on('click', function() {
                $tabMenu.removeClass(className);
                $(this).addClass(className);
                var index = $tabMenu.index(this);
                $(tabCon).hide();
                $(tabCon).eq(index).show();
            });
        },

        /**
         * 弹出iframe层，须要引入php端的 LayerAsset
         * 
         * @param  string  title 页面标题
         * @param  string  url   要打开的链接
         * @return integer
         */
        iframe: function (title, url) {
            if (typeof layer !== 'undefined') {
                var index = layer.open({
                    type: 2,
                    shadeClose: true,
                    title: title,
                    content: url,
                    zIndex: layer.zIndex,
                    success: function (layero) {
                        layer.setTop(layero);
                    }
                });
                layer.full(index);
                return index;
            }
        },
        /**
         * 初始化表单下所有checkbox、redio 的样式
         */
        initICheck: function () {
            $("form input[type=checkbox], form input[type=radio]").iCheck($.config('iCheck'));
        },
        /**
         * 初始化所有iframe点击事件
         */
        initIframe: function () {
            $("[class*='layer.iframe']").iframe();
        }
    });
    // 扩展后台全局方法
    $.fn.extend({
        /**
         * @see $.iframe()
         */
        iframe: function () {
            $(this).click(function () {
                var title = $(this).html(), 
                    url = $(this).attr('href');
                $.iframe(title, url);
                return false;
            });
        }
    });
    $.initICheck();
    $.initIframe();
});