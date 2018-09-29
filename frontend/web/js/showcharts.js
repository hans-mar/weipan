$(function () {
Highcharts.setOptions({
      global: {
          useUTC: false
      }
  });
   $('#container').highcharts('StockChart', {
    exporting:{  
    enabled:false //用来设置是否显示‘打印’,'导出'等功能按钮，不设置时默认为显示  
     },  

       colors: ['#e6262e', '#e6262e', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', 
'#FFF263', '#6AF9C4'] ,

    chart: {
                type: 'column',
                backgroundColor: 'rgba(0,0,0,0)'
            },
    title :{
    text:null
   },

      rangeSelector: {
               enabled:false,
               allButtonsEnabled: false
            },
         
         credits:{
           enabled:false // 禁用版权信息
          },
           exporting: {
            enabled: false
            },
        scrollbar: {
            enabled: false
        },
       navigator: {
            enabled: false
        },
        plotOptions: {
          areaspline:{
          color:'#e6262e',

          }

        },

            // xAxis: {
            //     breaks: [{ // Nights
            //         from: Date.UTC(2011, 9, 6, 16),
            //         to: Date.UTC(2011, 9, 7, 8),
            //         repeat: 24 * 36e5
            //     }, { // Weekends
            //         from: Date.UTC(2011, 9, 7, 16),
            //         to: Date.UTC(2011, 9, 10, 8),
            //         repeat: 7 * 24 * 36e5
            //     }]
            // },
             

            series : [{
                name : '价格',
                type: 'area',
                gapSize: 5,
                tooltip: {
                  backgroundColor:'#e6262e',
                    valueDecimals: 2
                },
                fillColor : {
                    linearGradient : {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops : [
                        [0, Highcharts.getOptions().colors[5]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[5]).setOpacity(0).get('rgba')]
                    ]
                },
                threshold: null
            }]
        });

// // setInterval( function(){
// var code= $("#product").val();
// var dataurl='/Addons/Ksource/showchart.php?pcode='+code;
//  console.log(dataurl);
// $.ajax({  
//                 type: "post",  
//                 url: dataurl,
//                 data:"pcode="+code,  
//                 async:false,  
//                 success: function(data) {
//                 var chart = $('#container').highcharts();
//                 var obj = JSON.parse(data);
//                 var ndata =eval(obj);
//              //   console.log(ndata);
//                  var price = new Array(); 
//                  var time=new Array();
//                  $.each(ndata, function(i,item){
//                    price.push(eval(item));
//                    console.log(eval(item));
//                   // // // console.log(item[0].substring(0,4));
//                    // time.push(eval(item)[0]);
//                   // //   // console.log(item[0].substring(11,16));
//                   });
//                 chart.series[0].setData(price); 
//                 // chart.xAxis.categories.push(time);
//                 // chart.xAxis[0].setCategories(time);
                  
//                 },  
//                 error: function(data) {  
                    
//                 }  
//             })        

// });  
// // },2000);
   

 // var code= $("#product").val();
var code=$('.select_circle_bot').data('pcode');
var dataurl='/Addons/Ksource/showcandle.php?pcode='+code+'&time=1';
 console.log(dataurl);
  $.ajax({  
                type: "post",  
                url: dataurl,
                data:"pcode="+code,  
                async:false,  
                success: function(data) {
                var chart = $('#container').highcharts();
                var obj = JSON.parse(data);
                var ndata =eval(obj);
                 var price = new Array(); 
                 var time=new Array();
                 $.each(ndata, function(i,item){
                   price.push(eval(item));
                  });
                chart.series[0].setData(price); 
                  
                },  
                error: function(data) {  
                    
                }  
            })  

$('.circle_bot').click(function(e){
   if ($(this).data('pcode')) {
        var code= $(this).data('pcode');
      } else {
        var code= $(".select_circle_bot").data('pcode');
      }
      location.href = $("#indexUrl").val() + '?pcode=' + code;
      return false;
    var dataurl='/Addons/Ksource/showcandle.php?pcode='+code+'&time=1';
    // console.log(dataurl);
    $.ajax({  
                type: "post",  
                url: dataurl,
                data:"pcode="+code,  
                async:false,  
                success: function(data) {
                var chart = $('#container').highcharts();
                var obj = JSON.parse(data);
                var ndata =eval(obj);
                 var price = new Array(); 
                 var time=new Array();
                 $.each(ndata, function(i,item){
                   price.push(eval(item));
                  });
                chart.series[0].setData(price); 
                  
                },  
                error: function(data) {  
                    
                }  
            })  
    });

});  