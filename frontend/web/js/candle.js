$(function () {
    var oldData;
    var candleChart;
    var x;
    var varyChart;
    var seHeight = $(window).height() - 295;
    $('#areaContainer').css('height',seHeight + 'px');
    $('#kContainer').css('height',seHeight + 'px');
    var data = {};
    var dayUnit = 5;
    var recMin = (new Date()).getMinutes();
    // 绑定tab栏事件
    $("#feature-tab li").click(function () {
        var $li = $(this),
            $a = $li.find('a'),
            unit = $a.data('unit');
        $li.addClass('active').siblings().removeClass('active');
        if (unit == -1) {
            $("#areaContainer").show();
            $("#kContainer").hide();
            $('.aniContainer').css("opacity","1")
            $(".my-btn-group").css("visibility","hidden");
        } else {
            $("#areaContainer").hide();
            $("#kContainer").show();
            getKlineStock(data, unit);
            //$('.aniContainer').css("display","none");
             $('.aniContainer').css("opacity","0")
             //$(".my-btn-group").css("visibility","visible");
             $(".my-btn-group").css("visibility","hidden");
        }
    });

    Highcharts.setOptions({
        global: {
            useUTC: false
        },
        colors: ['#7cb5ec', '#7cb5ec', '#7cb5ec', '#7cb5ec', '#7cb5ec', '#7cb5ec', '#7cb5ec', '#7cb5ec', '#7cb5ec']
    });
    
    $.get($("#getStockDataUrl").val(), {id: $("#productId").val()}, function (msg) {
        data[-1] = transData(msg);
        getAreaStock(data);
        $.get($("#getStockDataUrl").val(), {id: $("#productId").val(), unit: 'day'}, function (msg) {
            data[dayUnit] = transData(msg);
        });
    });
    //求plotItem的高度位置
    function setplotTop(chart,closeprice){
        //console.log(closeprice);
        var miny = chart.yAxis.min;
        var maxy = chart.yAxis.max;
        var svgHeight =  document.getElementsByClassName('highcharts-grid')[1].getBBox().height; 
        //console.log(svgHeight);
        //console.log(document.getElementsByClassName('highcharts-grid'));
        var domHeight = svgHeight / (maxy - miny) * (closeprice - miny) + 11;
        $('.aniContainer').css('bottom',domHeight + 'px').css("visibility","visible");
    }
    // 获取最新数据
    var flag = 0;
    var getPrice = function(chart, count) {
        //是否属于期货
        var data = {};
        data.json = $("#jsonData").html();
        data.pid = $("#productId").val();
        if ($('.selectProcut>li .price>span').html() == '休市') {
            return false;
        }
        $.get('/price.json?' + Math.random(), function(newData) {
            var nowProduct = $(".selectProcut>li.active").data('name'),
                price = Number(newData[nowProduct]),
                date = new Date(),
                minute = (new Date()).getMinutes(),
                x = Date.parse(date.format('yyyy/MM/dd hh:mm:ss')),
                length = chart.data.length;
                
                // if(!browser.versions['ios']){
                     if (length > 0) {
                        if (minute == recMin) {
                    
                            //console.log(flag);
                            var a = length - 1;
                            if(flag == 1){
                                var a = length - 2;
                            }
                            //获取当前坐标中的最大和最小值
                            var minVal = chart.yAxis.min;
                            var maxVal = chart.yAxis.max;
                            if(price > maxVal){
                                price = maxVal;
                            }else if(price < minVal){
                                price = minVal;
                            }
                            chart.data[a].y = price;
                            setplotTop(chart,price);
                            //chart.reflow();
                            chart.redraw();

                            //alert(123);
                        } else {
                            //alert(123);
                            flag = 1;
                            var minVal = chart.yAxis.min;
                            var maxVal = chart.yAxis.max;
                            if(price > maxVal){
                                price = maxVal;
                            }else if(price < minVal){
                                price = minVal;
                            }

                            chart.addPoint([x, price], true, true);
                            //alert(x  +  "*" + price);
                            setplotTop(chart,price);
                            recMin = minute;
                            //alert(321);
                        }
                    }
                // }
               
            $('.selectProcut>li').each(function(i , item){
                var $html = $($('.selectProcut>li')[i]).find(".price span").html();
                
                if( $html == '休市' ){
                    return;
                }
                if($(this).hasClass("active")){
                    beforePrice = $(this).find('.price>span').html();
                }
                var close = $(this).find('.price>span').html();
                var name = $(this).data('name');
                if (newData[name] != close) {
                    $(this).find('.price').removeClass('price-up');
                    $(this).find('.price').removeClass('price-down');
                    $(this).find('.arrow').removeClass('arrow-up');
                    $(this).find('.arrow').removeClass('arrow-down');
                }
                if (newData[name] > close) {
                    $(this).find('.price').addClass('price-up');
                    $(this).find('.arrow').addClass('arrow-up');
                } else if (newData[name] < close) {
                    $(this).find('.price').addClass('price-down');
                    $(this).find('.arrow').addClass('arrow-down');
                }
                $(this).find('.price>span').html(newData[name]);
            });
            ////////////////////////
            if(candleChart != undefined){
                getPrice1(candleChart);
            }
            /////////////////////

            
            setTimeout(function () {
                getPrice(chart);
            }, 2000);
        }, 'json');
    }
    var beforePrice ;
    function  getPrice1(chart, count) {
       // console.log(chart.data);
        //alert(1);
        /*if ($('.selectProcut>li .isTrade'+data.pid+'>span').html() == '休市') {
            return false;
        }*/
        if ($('.selectProcut>li .price>span').html() == '休市') {
            return false;
        }
        length = chart.data.length;
        //alert(length);
        if (length > 0) {
            //alert(123456);
            var price = beforePrice/*chart.data[chart.data.length - 1].price*/ 
            var newPrice = $(".price",$(".box_flex_1.active")).find("span").html();
            if (price == newPrice) {
                
            }else{
                var high = parseFloat(chart.data[chart.data.length - 1].high);
                var low = parseFloat(chart.data[chart.data.length - 1].low);
                var close = parseFloat(chart.data[chart.data.length - 1].close);
                var open = parseFloat(chart.data[chart.data.length - 1].open);
                if($(".price",$(".box_flex_1.active")).hasClass("price-down")){
                    //alert(123);
                    //close -= 5;
                    open -= 4;
                    if(close > open){
                        var temp = close;
                        close = open;
                        open = temp;
                    }
                    //open -= 5;
                }else if($(".price",$(".box_flex_1.active")).hasClass("price-up")){
                    //alert(321);
                    //close += 5;
                    open += 4;
                    if(close < open){
                        var temp = close;
                        close = open;
                        open = temp;
                    }
                }
                //alert([length - 1]);
                chart.data[length - 1].high = high;
                chart.data[length - 1].low = low;
                chart.data[length - 1].close = close;
                chart.data[length - 1].open = open;
                chart.redraw();
            }
            /*setTimeout(function () {
                getPrice1(chart);
            }, 2000);*/
        }
    }
    //自动隐藏已平仓的订单
    var getHideOrder = function() {
        //是否属于期货
        var pid = $("#productId").val();
        if ($('.selectProcut>li .price>span').html() == '休市') {
            return false;
        }
        $.post("/site/ajax-new-product-price", {pid: pid}, function(msg) {
            if (msg.state) {
                var idObj = msg.info;
                if(idObj.length != 0){
                    $('.myButtom .holdlist-wrap>ul>li').each(function(){
                        //被系统平仓的订单消失
                        var order_id = Number($(this).data('id'));
                        //判断此持仓id是否被系统平仓
                        if (idObj[order_id] == undefined) {
                            $(this).remove();
                        }
                    });
                } else {
                    $('.myButtom .holdlist-wrap>ul').html('');
                }
                setTimeout(function () {
                    getHideOrder();
                }, 2000);
            } else {
                $('.selectProcut>li').each(function(){
                    $(this).find('.price>span').html();
                    $(this).find('.price>i').remove();
                });
            }
        }, 'json');
    }

    function getAreaStock(data) {
        var length = data[-1].length;
        if (length > 30) {
            data = data[-1].slice(length - 30);
        } else {
            data = data[-1];
        }
        //console.log(data); 
        if(data[length-1]){
            var price = data[length-1][2]
        }
        $('#areaContainer').highcharts('StockChart', {
            chart: {
                type: 'areaspline',
                resetZoomButton: false,
                backgroundColor: 'rgba(0,0,0,0)',
                pinchType: "none", //禁用手势操作
                zoomType: "none",
                panning: false,
                events: {
                    load: function () {
                        //console.log(price);
                        var series = this.series;
                        if(price){
                            setTimeout(function(){
                                setplotTop(series[0],price);
                            },500);
                        }
                        setTimeout(function () {
                            getPrice(series[0]);
                            getHideOrder();
                        }, 2000);
                    }
                },
            },
            title: {
                text: ''
            },
            rangeSelector: {
                buttons: [{
                    type: 'hour',
                    count: 2,
                }],
                buttonTheme: {
                    style: {
                        display: 'none'
                    },
                },
                inputStyle: {
                    display: 'none'
                },
                labelStyle: {
                    display: 'none'
                },
                selected: 0,
            },
            XAxis: {
                reversed: true
            },
            yAxis: [{
                title: {
                    text: ''
                },
                labels: {
                    align: 'middle',
                    x: 2
                }, 
                opposite:false,
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            }, {
                title: {
                    enabled: false
                },
                gridLineWidth: 1,
                minorGridLineWidth: 1,
                minorTickInterval: 5,
                plotBands: [{
                    from: 0,
                    to: 25,
                    color: '#FCFFC5'
                }]
            }],
            // 图例
            legend: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            tooltip: {
                formatter: function() {
                    return '<b>' + Highcharts.dateFormat('%H:%M:%S', this.x) + '</b><br/><b>价格：</b>' + Highcharts.numberFormat(this.y, 2);
                },
                followPointer: true,
                followTouchMove: true
            },
            credits:{
                enabled: false
            },
            scrollbar: {
                enabled: false
            },
            navigator: {
                enabled: false
            },
            series : [{
                animation: false,
                name : 'price',
                type: 'areaspline',
                lineWidth : 1,
                data : data,
                tooltip: {
                    valueDecimals: 2
                },
                fillColor : {
                    linearGradient : {
                        x1: 0,
                        y1: 0,
                        x2: 1,
                        y2: 1
                    },
                    stops : [
                        [0, Highcharts.getOptions().colors[1]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[5]).setOpacity(0).get('rgba')]
                    ]
                },
                threshold: null
            }]
        });
    }

    function getKlineStock(data, unit) {
        var circle;
        switch (unit) {
            case 0:
                circle = 1;
                break;
            case 1:
                circle = 5;
                break;
            case 2:
                circle = 15;
                break;
            case 3:
                circle = 30;
                break;
            case 4:
                circle = 60;
                break;
            case dayUnit:
                circle = 60 * 24;
                break;
        }
        if (!data[unit]) {
            data[unit] = [];
            var diff = 60 * 1000 * circle;
            var start = data[-1][0][0],
                end = 0,
                sub = [0, 0, 0, 999999, 0];
            for (var key in data[-1]) {
                end = data[-1][key][0];
                if (end - start >= diff) {
                    sub[4] = data[-1][key - 1][4];
                    data[unit].push(sub);
                    start = data[-1][key][0];
                    sub = [0, 0, 0, 999999, 0];
                }
                if (end == start) {
                    sub[0] = data[-1][key][0];
                    sub[1] = data[-1][key][1];
                }
                if (sub[2] < data[-1][key][2]) {
                    sub[2] = data[-1][key][2];
                }
                if (sub[3] > data[-1][key][3]) {
                    sub[3] = data[-1][key][3];
                }
            }
        }
        //console.log(data[unit]);
        oldData = data[unit];
        x = oldData.length;
        $('#kContainer').highcharts('StockChart', {
            title: {
                text: ''
            },
            chart: {
                resetZoomButton: false,
                backgroundColor: 'rgba(0,0,0,0)',
                pinchType: "x", //禁用手势操作
                zoomType: "x",
                panning: false,
                events: {
                    load: function () {
                        var series = this.series;
                        candleChart = this.series[0];
                        setTimeout(function () {
                            //getPrice1(series[0]);
                            getHideOrder();
                        }, 0000);
                    }
                },
            },
            rangeSelector: {
                buttons: [{
                    type: 'minute',
                    count: 20,
                }, {
                    type: 'hour',
                    count: 1.5,
                }, {
                    type: 'hour',
                    count: 5,
                }, {
                    type: 'hour',
                    count: 40,
                }, {
                    type: 'hour',
                    count: 60,
                }, {
                    type: 'day',
                    count: 7,
                }],
                buttonTheme: {
                    style: {
                        display: 'none'
                    },
                },
                inputStyle: {
                    display: 'none'
                },
                labelStyle: {
                    display: 'none'
                },
                /*selected: unit,*/
            },
            scrollbar: {
                enabled: false
            },
            navigator: {
                enabled: false
            },
            credits:{
                enabled: false
            },
            tooltip: {
                formatter: function() {
                    var date, hour, minute;
                    var fix = function (num) {
                        if (num < 10) {
                            return '0' + num;
                        } else {
                            return num;
                        }
                    }
                    if (unit == dayUnit) {
                        date = Highcharts.dateFormat('%m-%d', this.x);
                    } else if (circle == 1) {
                        date = Highcharts.dateFormat('%m-%d', this.x) + '  ' + Highcharts.dateFormat('%H:%M', this.x);
                    } else {
                        minute = parseInt(Highcharts.dateFormat('%M', this.x));
                        minute = Math.round(minute / 5) * 5;
                        if (minute == 60) {
                            hour = parseInt(Highcharts.dateFormat('%H', this.x)) + 1;
                            date = fix(hour) + ':00';
                        } else {
                            date = Highcharts.dateFormat('%H:', this.x) + fix(minute);
                        }
                        date = Highcharts.dateFormat('%m-%d', this.x) + '  ' + date;
                    }
                    return '<b>' + date + '</b><br/>' + 
                           '<b>开盘价：</b>' + Highcharts.numberFormat(this.points[0]['point']['open'], 2) + '<br/>' + 
                           '<b>最高价：</b>' + Highcharts.numberFormat(this.points[0]['point']['high'], 2) + '<br/>' + 
                           '<b>最低价：</b>' + Highcharts.numberFormat(this.points[0]['point']['low'], 2) + '<br/>' + 
                           '<b>收盘价：</b>' + Highcharts.numberFormat(this.points[0]['point']['close'], 2);
                },
                followPointer: true,
                followTouchMove: true
            },
            plotOptions: {
                candlestick: {
                    upColor: '#f0302d',        // 涨 颜色
                    upLineColor: '#f0302d',    // 涨 线条颜色
                    color: '#17b03e',        // 跌 颜色
                    lineColor: '#17b03e'     // 跌 线条颜色
                }
            },
            xAxis: {
                labels: {
                    formatter: function () {
                        return Highcharts.dateFormat(unit == dayUnit ? '%m-%d' : '%H:%M', this.value);
                    },
                }
            },
            yAxis: [{
                labels: {
                    align: 'left',
                    x: 2
                }, 
                opposite:false,
                lineWidth: 1
            }],
            series: [{
                type: 'candlestick',
                name: $("#futuresName").val(),
                data: data[unit],
                dataGrouping: {
                    enabled: false
                },
                tooltip: {
                    valueDecimals: 2
                }
            }]

        },function(chart){
            varyChart = chart;
            /*$("body").click(function(){
                DynamicChangeTickInterval(chart , 100000);
            });*/
        });
    }

    var transData = function (data) {
        var arr = [];
        for (var i in data) {
            arr.push([
                parseInt(data[i]['time']), // the date
                parseFloat(data[i]['open']), // open
                parseFloat(data[i]['high']), // high
                parseFloat(data[i]['low']), // low
                parseFloat(data[i]['close']) // close
            ]);
        }
        return arr;
    }
    var browser={  
        versions:function(){  
            var u = navigator.userAgent, app = navigator.appVersion;  
            return {  
                trident: u.indexOf('Trident') > -1, //IE内核  
                presto: u.indexOf('Presto') > -1, //opera内核  
                webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核  
                gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1,//火狐内核  
                mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端  
                ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端  
                android: u.indexOf('Android') > -1 || u.indexOf('Adr') || u.indexOf('Linux') > -1, //android终端或者uc浏览器  
                iPhone: u.indexOf('iPhone') > -1 , //是否为iPhone或者QQHD浏览器  
                iPad: u.indexOf('iPad') > -1, //是否iPad  
                webApp: u.indexOf('Safari') == -1, //是否web应该程序，没有头部与底部  
                weixin: u.indexOf('MicroMessenger') > -1, //是否微信 （2015-01-22新增）  
                qq: u.match(/\sQQ/i) == " qq" //是否QQ  
            };  
        }(),  
        language:(navigator.browserLanguage || navigator.language).toLowerCase()  
    }  



    function DynamicChangeTickInterval(chart,interval) {
        var data = oldData[1];
        chart.series[0].setData(data);  
        chart.redraw(false);  
        tes(chart.series[0]);
    }
    var z = 0;
    $("#plus-btn").click(function(){
        z ++;
        var data = [];
        for(var i = oldData.length - z;i>0;i--){
            data.push(oldData[i]);
        }
        x = data.length;
        if(data.length <= 0){
            return;
        }
        varyChart.series[0].setData(data);
        varyChart.redraw(); 
    });
    $("#minus-btn").click(function(){
        x ++;
        if(x >= oldData.length){
            return;
        }
        var data = [];
        for(var i = 0;i < x;i++){
            data.push(oldData[i]);
        }
        z = x - data.length;
        varyChart.series[0].setData(data);
        varyChart.redraw(); 
    });
    $("#back-btn").click(function(){
        varyChart.series[0].setData(oldData);
        varyChart.redraw(); 
        z = 1;
        x = oldData.length;
    });

});
