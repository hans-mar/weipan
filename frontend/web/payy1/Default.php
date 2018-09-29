<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head id="Head1" >

    <title></title>
    <style type="text/css">
        .style1
        {
            width: 681px;
        }
        .style2
        {
            width: 158px;
            text-align: right;
        }
        .style3
        {
            width: 455px;
        }
    </style>
</head>
<body>
    <form action="pay.php" method="post" id="form1" >
    <div>
    
        <table class="style1">
            <tr>
                <td class="style2">
                    商户ID:</td>
                <td class="style3">
                    <input name="txtpartner" type="text" ID="txtpartner" value="1735"  Width="214px">
                </td>
            </tr>
            <tr>
                <td class="style2">
                    商户KEY:</td>
                <td class="style3">
                    <input type="text" ID="txtKey" name="txtKey" value="d2ded0eea5874e6eb5e2a9c7d7552a68"  Width="214px"></td>
            </tr>
            <tr>
                <td class="style2">
                    银行类型:</td>
                <td class="style3">
                    <input name="txtbanktype" type="text" ID="txtbanktype" value="30002" >
                </td>
            </tr>
            <tr>
                <td class="style2">
                    订单金额:</td>
                <td class="style3">
                    <input name="txtpaymoney" type="text" ID="txtpaymoney" value="10" >
                </td>
            </tr>
            <tr>
                <td class="style2">
                    订单号码:</td>
                <td class="style3">
                    <input name="txtordernumber" type="text" ID="txtordernumber" value="<?php echo date("YmdHis")?>" >
                </td>
            </tr>
            <tr>
                <td class="style2">
                    异步通知地址:</td>
                <td class="style3">
                    <input name="txtcallbackurl" type="text" ID="txtcallbackurl" value="http://www.100gift.cn/payy/callback.php"  Width="362px">
                </td>
            </tr>
            <tr>
                <td class="style2">
                    同步跳转地址:</td>
                <td class="style3">
                    <input name="txthrefbackurl" type="text" ID="txthrefbackurl" value="http://www.100gift.cn/payy/jump.php"  Width="362px">
                </td>
            </tr>            <tr>
                <td class="style2">
                    备注信息:</td>
                <td class="style3">
                    <input name="txtattach" type="text" ID="txtattach" value="mygod" >
                </td>
            </tr>
            <tr>
                <td class="style2">&nbsp;
                    </td>
                <td class="style3">
                    
                    <input type="submit"  value="提交支付"/>
                </td>
            </tr>
            <tr>
                <td class="style2">&nbsp;
                    </td>
                <td class="style3">&nbsp;
                    </td>
            </tr>
            <tr>
                <td class="style2">&nbsp;
                    </td>
                <td class="style3">&nbsp;
                    </td>
            </tr>
        </table>
    
    </div>
    </form>
</body>
</html>
