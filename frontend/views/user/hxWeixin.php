<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>微信支付</title>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="black" name="apple-mobile-web-app-status-bar-style" />
<meta content="telephone=no" name="format-detection" />
<meta name="viewport" content="minimal-ui=yes,width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
<meta name="viewport" content="minimal-ui=yes,width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
<style type="text/css">

body {
  font: 100%/1.4 Verdana, Arial, Helvetica, sans-serif;
  background-color: #ededed;
  margin: 0;
  padding: 0;
  color: #000;
}
/* ~~ 元素/标签选择器 ~~ */
ul, ol, dl { /* 由于浏览器之间的差异，最佳做法是在列表中将填充和边距都设置为零。为了保持一致，您可以在此处指定所需的数值，也可以在列表包含的列表项（LI、DT 和 DD）中指定所需的数值。请记住，除非编写一个更具体的选择器，否则，在此处进行的设置将层叠到 .nav 列表。 */
  padding: 0;
  margin: 0;
}
h1, h2, h3, h4, h5, h6, p {
  margin-top: 0;   /* 删除上边距可以解决边距会超出其包含的块的问题。剩余的下边距可以使块与后面的任何元素保持一定距离。 */
  padding-right: 15px;
  padding-left: 15px; /* 向块内的元素侧边（而不是块元素自身）添加填充可避免使用任何方框模型数学。此外，也可将具有侧边填充的嵌套块用作替代方法。 */
}
a img { /* 此选择器将删除某些浏览器中显示在图像周围的默认蓝色边框（当该图像包含在链接中时） */
  border: none;
}
</style>
</head>

<style type="text/css">
input {-webkit-appearance:none; /*去除input默认样式*/}
input[type="submit"],

input[type="reset"],

input[type="button"],

input{-webkit-appearance:none;}
a{color:#333;text-decoration:none;}

   .container{font-family:微软雅黑; }
   .order{height:30px;line-height:30px;}
   .price{height:40px;line-height:40px;font-size:2.5em;margin:20px;}
   .price span{margin-left:20px;font-weight:bolder;}
   .shop{border-top:solid 1px #dedede;border-bottom:solid 1px #dedede;width:100%;padding:10px 0;background:#fff;font-size:12px;
   }
   .btn{
     width:95%; height:45px; border-radius: 5px;background-color:#25ab28; border:0px #FE6714 solid; cursor: pointer; font-weight:bold;color:white;  font-size:16px;margin-top:20px;
     }
  </style>
<body>

<div class="container">

    <div align="center" style="margin-top:50px;">
        <div class="order">订单号：<?= $info['userCharge']->trade_no ?></div>
        <div class="price">¥<span><?= $info['userCharge']->amount ?></span></div>
        <div class="shop">
         <table align="center" width="95%">
          <tr><td style="color:#888">收款方</td><td align="right">微信支付</td></tr>
         </table>
        </div>
    
    <?= $info['payLinks'] ?>
  </div>
</div>
 
</body>
</html>
