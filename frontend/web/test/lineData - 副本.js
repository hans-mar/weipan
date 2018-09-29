var chart = {
	data: {
		proNo: $('.mid').data('proNo'),
		productId: $('.mid').data('productId'),
		lineData: [],//分时线
		redLineData: [],//分时红线
		allLineData: [],//全天线
		lightnLineData: [],//闪电
		kLineData: [],//K线
		mKLineData: [],//分钟K线
		//k线数据
		ohlc: [],
		volume: [],
		kLine: [],
		oldClosePrice: 0,
		lastTime: null,
		lastPrice: null,
		endTime: null,
		chart: null,
		dayChart: null,
		sChart: null,
		kChart: null,
		mKType: 0,
		//颜色
		c1: '#fff',
		c2: '#eee',
		c3: '#aaa',
		//定时器
		s: null,
		m: null
	},
	pan: function(model) {
		chart.data.oldClosePrice = model.preClose;
		for (var prop in model) {
			var val = model[prop];
			if(val == null){
				val = '';
			}
			var $pan = $('[data-pan-' + prop + ']');
			$pan.each(function() {
				if ($(this).data('color') != undefined) {
					$(this).removeClass('col-up col-down');
					if ($(this).data('color') == '0') {
						$(this).addClass(val >= 0 ? 'col-up' : 'col-down');
					} else if ($(this).data('color') == 'price') {
						$(this).addClass(val >= chart.data.oldClosePrice ? 'col-up' : 'col-down');
					}
				}
				$(this).text(val + (val == '' ? '' : ($(this).data('end') == undefined ? '' : $(this).data('end'))));
			})
			var $ratio = $('[data-ratio-' + prop + ']');
			$ratio.each(function() {
				$(this).css('width', (val >= 100 ? 100 : val) + (val == '' ? '' : '%'));
			})
		}

		/*var secDataForm = $("#secDataForm").find("em")
		var nowPriceShow = $("#nowPriceShow")
		var udPrice = $("#udPrice")
		var udPercent = $("#udPercent")
		todayOpen = model.open // 今开
		// 昨收
		maxPrice = model.high // 最高低价格
		minPrice = model.low // 最高低价格
		name = model.name // 产品名
		var nowPrice = model.indices // 现价
		var udPriceData = (nowPrice - chart.data.oldClosePrice)
		var udPercentData = ((nowPrice / chart.data.oldClosePrice - 1) * 100)
				

		// 更新最新价格，赋予颜色
		if (nowPrice > chart.data.oldClosePrice) {
			nowPriceShow.removeClass("col-down").addClass("col-up")
			udPrice.removeClass("col-down").addClass("col-up")
			udPercent.removeClass("col-down").addClass("col-up")
			nowPriceShow.text(nowPrice)
			udPrice.text("+" + udPriceData)
			udPercent.text("+" + udPercentData + "%")
		} else {
			nowPriceShow.addClass("col-down").removeClass("col-up")
			udPrice.addClass("col-down").removeClass("col-up")
			udPercent.addClass("col-down").removeClass("col-up")
			nowPriceShow.text(nowPrice)
			udPrice.text(udPriceData)
			udPercent.text(udPercentData + "%")
		}*/
	},
	indices: function() {
		$.ajax({
			type : "get",
			url : "/hq/getHQ",
			data : {
				proNo : chart.data.proNo,
				panType : 1
			},
			async : true,
			success : function(data) {
				if(data.success){
					var newData = data.resultObject
					var model = newData.model
					if(!newData.isOpen){
						$(".buy-btn .goBuy").addClass("btn-hui")
						$('[data-pan-sell]').parent().data('url', '');
						$('[data-pan-buy]').parent().data('url', '');
					}
					chart.pan(model)
					var x = model.dateTime,
					y = model.indices
					if(chart.data.sChart != null){
						var sChart = chart.data.sChart
						var length = sChart.series[0].data.length
						if(length > 0 && x > sChart.series[0].data[length-1].x){
							sChart.series[0].addPoint([x, y], false, true)
							sChart.series[1].addPoint([x, y], true, true)
						}
						
					}
					
					if (chart.data.lineData.length > 0) {
						var redLength = chart.data.redLineData.length
						if(redLength > 0){
							for (i = 0; i < chart.data.redLineData.length; i++) {
								chart.data.redLineData[i].y = y
							}
							var backgroundColor
							if(y < chart.data.oldClosePrice){
								backgroundColor = 'rgba(45,188, 122, 0.8)';
							}else{
								backgroundColor = 'rgba(251,10, 10, 0.75)';
							}
							chart.data.redLineData[redLength -1].dataLabels.backgroundColor = backgroundColor;
						}
						
						chart.data.lineData[chart.data.lineData.length - 1].y = y;

						chart.data.chart.series[0].setData(chart.data.lineData, false, false, false)
						chart.data.chart.series[1].setData(chart.data.redLineData,false, false, false) // 数据更新
						chart.data.chart.series[2].setData(chart.data.lineData, true, false, false)

					}
					
				}else{
					$('[data-pan-sell]').parent().data('url', '');
					$('[data-pan-buy]').parent().data('url', '');
					
					$(".buy-btn .goBuy").addClass('btn-hui');
					layer.open({
						content : data.msg,
						skin : 'msg',
						time : 1.5
					});
					clearInterval(chart.data.s)
					clearInterval(chart.data.m)
				}
			}
		})
	},
	queryLine: function() {
		$.ajax({
			type : "get",
			url : "/hq/getMinKLine",
			data : {
				proNo : chart.data.proNo,
				time : chart.data.lastTime == null ? '' : chart.data.lastTime + 1000
			},
			success : function(data) {
				var list = data.resultList
				var obj = data.resultObject
				if(chart.data.endTime != null && chart.data.endTime != obj.endTime){
					location.reload();
				}
				chart.data.endTime = obj.endTime;
				if(list.length > 0){
					// 最后有值的价格
					chart.data.lastPrice = list[list.length - 1].indices;
					chart.data.lastTime = list[list.length - 1].time;
					// 拼接折线图，蓝线和红线
					$.each(list, function() {
//						var time = Number(this.time) + 3600 * 8 * 1000;
						var nowPrice = this.indices;

						chart.data.lineData.push({
							x : this.time,
							y : nowPrice
						})
						if(chart.data.allLineData.length > 0){
							chart.data.allLineData.push({
								x : this.time,
								y : nowPrice
							})
						}
					})
					chart.data.redLineData = [];
					for (var i = chart.data.lastTime; i <= chart.data.endTime; i += 60000) {
//						var time = i + 3600 * 8 * 1000;
						chart.data.redLineData.push({
									x : i,
									y : chart.data.lastPrice
								})
					}
					// 红线最后一点展示价格，默认绿色
					var dataLabels = {
						enabled : true,
						borderRadius : 2,
						x : 5,
						y : 18,
						backgroundColor : 'rgba(251,10, 10, 0.75)',
						padding : 1,
						zIndex : 100,
						style : {
							color : '#FFFFFF',
							textOutline : 'none',
							fontWeight : 'normal'
						}
					}
					// 如果大于收盘价则修改为红色
					if (chart.data.redLineData[chart.data.redLineData.length - 1].y < chart.data.oldClosePrice) {
						dataLabels.backgroundColor = 'rgba(45,188, 122, 0.8)';
					}
					// 红线跳动的红点
					var redStart = {
						y : 12.3,
						enabled : true,
						useHTML : true,
						formatter : function() {
							return '<span class="hd"></span>'
						}
					}
					if (chart.data.redLineData.length > 0) {
						chart.data.redLineData[0].dataLabels = redStart;
						chart.data.redLineData[chart.data.redLineData.length - 1].dataLabels = dataLabels;
					}
					// 动态更新数据
					chart.data.chart.series[0].setData(chart.data.lineData, false)
					chart.data.chart.series[1].setData(chart.data.redLineData, false)
					chart.data.chart.series[2].setData(chart.data.lineData, true)
					if(chart.data.dayChart != null){
						chart.data.dayChart.series[0].setData(chart.data.allLineData, false)
						chart.data.dayChart.series[2].setData(chart.data.allLineData)
					}
				}
			}
		})
	},
	queryLightn: function() {
		$.ajax({
			type: 'get',
			url: '/hq/getSecondLine',
			data: {
				proNo : chart.data.proNo
			},
			success: function(data) {
				if(data.success){
					var list = data.resultList
					$.each(list, function(){
						/*var repeat = true
						var obj = this*/
						/*$.each(chart.data.lightnLineData, function(){
							if(obj.time == this.x){
								repeat = false
								return false
							}
						})*/
//						if(repeat){
							chart.data.lightnLineData.push({
								x: this.time,
								y: this.indices
							})
//						}
						
					})
					var sChart = chart.data.sChart
					sChart.series[0].setData(chart.data.lightnLineData)
					sChart.series[1].setData(chart.data.lightnLineData)
				}
			}
		})
	},
	queryAllDayLine: function() {
		$.ajax({
			type : "get",
			url : "/hq/getMinKLine",
			data : {
				proNo : chart.data.proNo,
				isAllDay: true
			},
			success : function(data) {
				var list = data.resultList
				var obj = data.resultObject
				$.each(list, function() {
					chart.data.allLineData.push({
						x : this.time,
						y : this.indices
					})
				})
				var line = [{
					x: obj.endTime,
					y: null
				}];
				chart.data.dayChart.series[0].setData(chart.data.allLineData)
				chart.data.dayChart.series[1].setData(line)
				chart.data.dayChart.series[2].setData(chart.data.allLineData)
			}
			
		})
	},
	dayLine: function() {
		chart.data.dayChart = Highcharts.chart('dayLine', chart.chartOption())
//		chart.data.dayChart.series[1].remove(true)
		chart.queryAllDayLine()
	},
	lightnLine: function() {
		chart.data.sChart = Highcharts.chart('lightnLine', chart.chartOption('line'))
		chart.data.sChart.series[1].remove(true)
		chart.queryLightn()
	},
	init: function() {
		//黑色主题下修改则线图颜色
		if($(".index-head").css("background-color") == "rgb(29, 29, 29)"){
			chart.data.c1 = "#1d1d1d";
			chart.data.c2 = "#292929";
			chart.data.c3 = "#555";
		}
		
		
		chart.data.chart = Highcharts.chart('mLine', chart.chartOption())
		// 定时请求数据
		chart.data.s = setInterval(function() {
					chart.indices()
				}, 1000)
		chart.data.m = setInterval(function() {
					chart.queryLine()
				}, 60000)
		chart.indices()
		chart.queryLine()
		Highcharts.setOptions({
		    global: {
		        useUTC: false
		    },
		    lang: {
		    	resetZoom: '原比例'
		    }
		});
		
		
		
	},
	chartOption: function(type) {
		var formatStr = '%H:%M'
		if(!type){
			type = 'area'
		}else{
			formatStr = '%H:%M:%S'
		}
			
		return {
			chart : {
				// 关闭滑动缩放等
				panning : false,
				zoomType : 'none',
				pinchType : 'none',
				spacing : [0, 0, 5, 0],
				backgroundColor: chart.data.c1
			},
			credits : {
				enabled : false
				// 不显示LOGO
			},
			title : false,
			xAxis : {
	            tickColor: chart.data.c3,
	            lineColor: chart.data.c3,
				showFirstLabel : true,
				showLastLabel : true,
				labels : {
					style : { // 字体样式
						font : 'normal 12px Verdana, sans-serif'
					},
					formatter : function() {
						var returnTime = Highcharts.dateFormat(formatStr, this.value);
						return '<span style="font-size:10px;color:#aaa">'
								+ returnTime + '</span>';
					},
					y : 15,
					step : 1,
					useHTML : true
				},
				tickLength : 7,
				gridLineDashStyle : 'soild',
				gridLineColor : chart.data.c2,
				gridLineWidth : 1
			},
			tooltip : {
				useHTML : true,
				formatter : function() {
					var _date = Highcharts.dateFormat(formatStr, this.x);
					// 如果是红色线图则禁用弹出层
					if (type != 'line' && this.series.options.type == 'line') {
						return false;
					}
					var _y = this.y;
					if (_y == chart.data.oldClosePrice) {
						return '<p style="margin:0px;padding:0px;font-size:14px;font-weight:bold"><span>'
								+ name
								+ '   </span><p style="color:#666">'
								+ _date
								+ '</p></p><p style="margin:0px;padding:0px;">'
								+ '<p style="margin:0px;padding:0px;">价格：'
								+ _y
								+ '</p>';
					} else if (_y > chart.data.oldClosePrice) {
						return '<p style="margin:0px;padding:0px;font-size:14px;font-weight:bold"><span>'
								+ name
								+ '   </span><p style="color:#666">'
								+ _date
								+ '</p></p><p style="margin:0px;padding:0px;">'
								+ '<p style="margin:0px;padding:0px;">价格：'
								+ '<em style="color:#FF5555">'
								+ _y
								+ '</em>'
								+ '</p>';
					} else {
						return '<p style="margin:0px;padding:0px;font-size:14px;font-weight:bold"><span>'
								+ name
								+ '   </span><p style="color:#666">'
								+ _date
								+ '</p></p><p style="margin:0px;padding:0px;">'
								+ '<p style="margin:0px;padding:0px;">价格：'
								+ '<em style="color:#00CC66">'
								+ _y
								+ '</em>'
								+ '</p>';
					}
				}
			},
			yAxis : [{
				// tickPixelInterval: 100,
				opposite : true, // 是否把它显示到另一边（右边）
				tickPositioner : function() { // 只显示最大最小两个
					var positions = [this.tickPositions[0],
							this.tickPositions[this.tickPositions.length - 1]];
					return positions;
				},
	
				title : {
					text : null
				},
				labels : {
					align : 'right',
					x : -6,
					y : 12,
					useHTML : true,
					formatter : function() {
						return this.isFirst ? '<span class="firstShow">'
								+ this.value + '</span>' : this.value
					}
	
				},
				// showFirstLabel: false,
				gridLineColor : chart.data.c2
			}, {
	
				showLastLabel : true,
				gridLineWidth : 0,
				title : {
					text : null
				},
				labels : {
					align : 'left',
					x : 6,
					y : 12,
	
					useHTML : true,
					formatter : function() {
	
						var node = this.isFirst
								? '<span class="firstShowRatio" '
								: '<span '
						if (this.value > 0) {
							node += "style='color:#FF5555'>" + this.value
									+ "%</span>"
						} else if (this.value < 0) {
							node += "style='color:#00CC66'>" + this.value
									+ "%</span>"
						} else {
							node += ">" + this.value + "%</span>"
						}
						return node;
	
					}
				},
				tickPositioner : function() { // 无界限，百分比
				// var num = (maxPrice-minPrice)/2;
					// 上下边距 根据产品价格变化
					var list = [];
					var positions = [this.tickPositions[0],
							this.tickPositions[this.tickPositions.length - 1]];
					$.each(positions, function() {
								list.push(((this - chart.data.oldClosePrice) / chart.data.oldClosePrice * 100).toFixed(2));
							})
					return list;
				}
				
			}],
			legend : {
				enabled : false
			},
			plotOptions : {
				series : {
					/* 关闭动画 */
					animation : false
				},
	
				// 线图设置
				line : {
					states : {
						hover : {
							lineWidth : 1,
							enabled : false,
							halo : false
						}
					},
					lineWidth : .8
				},
	
				// 区域图设置
				area : {
					fillColor : {
						linearGradient : {
							x1 : 0,
							y1 : 0,
							x2 : 0,
							y2 : 1
						},
						stops : [
								[0, Highcharts.getOptions().colors[0]],
								[
										1,
										Highcharts
												.Color(Highcharts.getOptions().colors[0])
												.setOpacity(0).get('rgba')]]
					},
					marker : {
						enabled : false
					},
					lineWidth : 1,
					states : {
						hover : {
							lineWidth : 1
						}
					},
					threshold : null
				}
			},
	
			series : [{
						type : type,
						yAxis : 0,
						turboThreshold: 9999
					}, {
						type : 'line',
						lineWidth : 1,
						color : "#f34950",
						dashStyle : 'Dash'
					}, {
						yAxis : 1
					}]
		}
	},
	queryKLine: function() {
		$.ajax({
			type : "get",
			url : "/hq/getDailyKLine",
			data : {
				proNo : chart.data.proNo
			},
			async : true,
			success : function(data) {
				var list = data.resultList
				chart.data.kLineData = list
				chart.kLineData()
			}
		})
	},
	kLineData: function() {
		if(chart.data.kLineData.length == 0){
			chart.queryKLine()
			return false
		}
		chart.data.ohlc = []
		chart.data.volume = []
		chart.data.kLine = []
		$.each(chart.data.kLineData, function(){
			chart.data.ohlc.push([
				this.dateTime, // the date
				this.open, // open
				this.high, // high
				this.low, // low
				this.close // close
			])
			chart.data.volume.push([
				this.dateTime, // the date
				this.vol // the volume
			]);
			chart.data.kLine.push([
				this.dateTime,// the date
		        this.close// close
		    ]);
		})
		chart.createKLine()
	},
	queryMKLineData: function(type) {
		$.ajax({
			type : "get",
			url : "/hq/getMinuteKLine",
			data : {
				proNo : chart.data.proNo
			},
			async : true,
			success : function(data) {
				var list = data.resultList
				chart.data.mKLineData = list
				chart.mKLineData(type)
				setInterval(function() {
					chart.queryMinute()
				}, 60000)
				
			}
		})
	},
	queryMinute: function() {
		var data = chart.data.mKLineData
		var length = data.length
		var time = data[data.length-1].dateTime
		if(length > 0){
			$.ajax({
				type: 'get',
				url: '/hq/getMinuteKLineByTime',
				data: {
					proNo: chart.data.proNo,
					time: time
				},
				success: function(data) {
					if(data.resultList.length > 0){
						chart.data.mKLineData = chart.data.mKLineData.concat(data.resultList)
						chart.mKLineData(chart.data.mKType)
					}
						
				}
			})
		}
		
	},
	mKLineData: function(type) {
		if(!type)
			return false
		type = Number(type)
		if(chart.data.mKLineData.length == 0){
			chart.queryMKLineData(type)
			return false
		}
		var list = chart.data.mKLineData
//		type--
		chart.data.ohlc = []
		chart.data.volume = []
		chart.data.kLine = []
		for(var i=type-1; i<list.length; i=i+type){
			/*if(type > i)
				continue*/
			var high = 0,low = 999999999,open,close,vol = 0,date
			for(var j=0; j<type; j++){
				vol += list[i - j].vol
				if(list[i - j].high > high)
					high = list[i - j].high
				if(list[i - j].low < low)
					low = list[i - j].low
			}
			open = list[i].open
			close = list[i - type + 1].close
			dateTime = list[i - type + 1].dateTime
			chart.data.ohlc.push([
				dateTime, // the date
				open, // open
				high, // high
				low, // low
				close // close
			])
			chart.data.volume.push([
				dateTime, // the date
				vol // the volume
			]);
			chart.data.kLine.push([
				dateTime,// the date
		        close// close
		    ]);
		    
		}
		chart.createKLine(type)
	},
	kLine: function(type) {
		chart.data.mKType = type
		if(type == 0){
			chart.kLineData()
			return false
		}else if(type){
			chart.mKLineData(type)
			return false
		}
	},
	createKLine: function(type) {
		var format = '%Y-%m-%d';
		if(type)
			format = '%H:%M:%S';
		//平均线
		function calculateMA(dayCount, data) {
		    var result = [];
		    for (var i = 0, len = data.length; i < len; i++) {
		        if (i < dayCount) {
		            result.push([data[i][0],null]);
		            continue;
		        }
		        var sum = 0;
		        for (var j = 0; j < dayCount; j++) {
		            sum += data[i - j][1];
		        }
		        result.push([data[i][0], sum / dayCount]);
		    }
		    return result;
		}
		// 修改源码，改变柱状图颜色
		var originalDrawPoints = Highcharts.seriesTypes.column.prototype.drawPoints;
		Highcharts.seriesTypes.column.prototype.drawPoints = function() {
			var merge = Highcharts.merge, series = this, chart = this.chart, points = series.points, i = points.length;
			while (i--) {
				var candlePoint = chart.series[1].points[i];
				var candlePoint1 = chart.series[0].points[i];
				if (candlePoint1.open != undefined
						&& candlePoint1.close != undefined) {
					var color = candlePoint1.open < candlePoint1.close
							? '#e82e45'
							: '#27ab7b';
					candlePoint.color = color
				} else {
					var seriesPointAttr = merge(series.pointAttr);
				}
				points[i].pointAttr = seriesPointAttr;
			}
			originalDrawPoints.call(this);
		}

		// create the chart
		$('#kLine').highcharts('StockChart', {
			chart: {
				panning : false,
				zoomType : 'x',
//				pinchType : 'none',
				spacing : [0, 0, 10, 0],
				backgroundColor: chart.data.c1
			},
			rangeSelector : {
				enabled : false
				// 禁用切换按钮
			},
			credits : {
				enabled : false
				// 禁用LOGO
			},
			navigator : {
				enabled : false
				// 禁用底部滚动区域
			},
			scrollbar : {
				enabled : false
				// 禁用滚动条
			},
			title : {
				text : null
				// 禁用标题
			},
			plotOptions : { // 设置属性，样式
				candlestick : {
					upColor : '#e82e45',
					upLineColor : '#e82e45',
					color : '#27ab7b',
					lineColor : '#27ab7b'
				}
			},
			tooltip : {
				formatter : function() {
					var time = Highcharts.dateFormat(format, this.x);
					var t = '<em style="font-weight:bold">' + time
							+ '</em>';
					var p = this.points
					t += '<br/>最高:' + p[0].point.high + '<br/>最低:'
							+ p[0].point.low + '<br/>开盘:' + p[0].point.open
							+ '<br/>结算:' + p[0].point.close + '<br/>成交量：'
							+ p[1].point.y
					return t;
				}
			},
			xAxis : {
				tickColor: chart.data.c3,
                lineColor: chart.data.c3,
				dateTimeLabelFormats : {
					millisecond : '%H:%M:%S.%L',
					second : '%H:%M:%S',
					minute : '%H:%M',
					hour : '%H:%M',
					day : '%y-%m-%d',
					week : '%m-%d',
					month : '%y-%m',
					year : '%Y'
				}
			},
			yAxis : [{
						labels : {
							align : 'right',
							x : -3
						},
						/*
						 * title: { text: '价格' },
						 */
						gridLineColor: chart.data.c2,
						height : '80%'
					}, {
						labels : {
							align : 'right',
							x : -3
						},
						/*
						 * title: { text: '成交量' },
						 */
						gridLineColor: chart.data.c2,
						top : '81%',
						height : '19%'
					}],
					// 线图设置
					line : {
						states : {
							hover : {
								lineWidth : 1,
								enabled : false,
								halo : false
							}
						}
					},
					legend: false
						/*{
						enabled : true,
				        borderWidth: 0,
				        itemMarginTop:-10,
				        itemDistance:11,
				        itemMarginBottom:-10,
				        itemStyle:{
				        	color:"#777",
				        	fontWeight:400,
				        	fontSize:"10px"
				        }
				    }*/,
			series : [{
						type : 'candlestick',
						data : chart.data.ohlc,
						showInLegend: false
					},{
						type : 'column',
						data : chart.data.volume,
						yAxis : 1,
						showInLegend: false
					}, {
						name: 'MA5',
						type : 'line',
						lineWidth : 1,
						color : "#fb3c7f",
						dashStyle : 'Solid',
						data :calculateMA(5, chart.data.kLine)
					},{
						name: 'MA10',
						type : 'line',
						lineWidth : 1,
						color : "#269bd7",
						dashStyle : 'Solid',
						data :calculateMA(10, chart.data.kLine)
					},{
						name: 'MA20',
						type : 'line',
						lineWidth : 1,
						color : "#face00",
						dashStyle : 'Solid',
						data :calculateMA(20, chart.data.kLine)
					},{
						name: 'MA40',
						type : 'line',
						lineWidth : 1,
						color : "#27d092",
						dashStyle : 'Solid',
						data :calculateMA(40, chart.data.kLine)
					},{
						name: 'MA60',
						type : 'line',
						lineWidth : 1,
						color : "#812f88",
						dashStyle : 'Solid',
						data :calculateMA(60, chart.data.kLine)
					}]
		});
	}
}