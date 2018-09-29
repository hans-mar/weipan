

<!DOCTYPE html>
<html>
    <head>
        <base href="http://28zrysh.wellwind.net:80/memberdcb/">
        <meta http-equiv="Content-Type" content="applicationnd.wap.xhtml+xml;charset= UTF-8" />
        <meta http-equiv="expires" content="2678400" />
        <meta name="format-detection" content="telephone=no" />
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
        <meta name="keywords" content="手机版软件,微交易,期货,贵金属,财经,白手理财,投资" />
        <meta name="description" content="纵览行情，明确策略，实时专家互动，知晓天下财经" />
        <title>云交易</title>
        <link href="http://28zrysh.wellwind.net:80/memberdcb/styles/main.css" type="text/css" rel="stylesheet" />
        <link href="http://28zrysh.wellwind.net:80/memberdcb/styles/guize.css" type="text/css" rel="stylesheet" />
        <script src="http://28zrysh.wellwind.net:80/memberdcb/scripts/prepare.js?t=v2.1.0.1-1469855499691" type="text/javascript"></script>
        <script type="text/javascript">
            function showTable(obj){
                for( var i=1;i<=6;i++){
                    document.getElementById("tableDiv"+i) && (document.getElementById("tableDiv"+i).style.display="none");
                    document.getElementById("jiantou"+i) && (document.getElementById("jiantou"+i).style.display="none");
                    document.getElementById("gz_on"+i) && (document.getElementById("gz_on"+i).className="");
                }
                document.getElementById("tableDiv"+obj).style.display="";
                document.getElementById("jiantou"+obj).style.display="";
                document.getElementById("gz_on"+obj).className="gz_on";
            }

            function changeTab(index, e){
                /*var nodes = document.querySelectorAll(".gz_tab li");
                for(var i in nodes){
                    nodes[i].className = "";
                }
                e.className = "active";*/
                showTable(index==0? 1:5);
                for( var i=0;i<2;i++){
                    document.getElementById("gz_tab_content" + i).style.display="none";
                    document.getElementById("gz_tab"+i).style.borderBottom="0";
                }
                document.getElementById("gz_tab"+index).style.borderBottom="2px solid red";
                document.getElementById("gz_tab_content" + index).style.display="block";
            }
        </script>
        <style type="text/css">
            .gz_blo1, .gz_blo2 {padding: 15px 10px;}
            #gz_on5,#gz_on6{width:50%;}
            .gz_tab{width:100%; padding-bottom: 6px;}
            #gz_tab0{border-bottom:2px solid red;}
            .gz_tab li{width:50%;text-align:center;display:inline-block;font-size:18px; box-sizing: border-box; border-left: 1px solid #ccc; border-right: 1px solid #ccc; padding: 6px 0; background-color: #fff;}
            .gz_tab li:first-child{border-left: 0; }
            
            .gz_tab li.active{
                border-bottom: 2px solid red;
            }
            
            .gz_tab li:last-child{border-right: 0; }
            .gz_tab li span{padding:3px 10px;}
        </style>
    </head>
    <body>
        <!--微盘规则-->
        <div class="rule_box">
            <div class="rule_top"><img src="activity/images/banner.png" /></div>
            <div class="gz_cont">
                <div id="gz_tab_content1">
                    <div class="gz_tit clearfix">
                        <ul>
                            <li id="gz_on5" class="gz_on">
                                <a href="javascript:void(0)" onclick="showTable(5)">交易规则</a>
                                <span class="jiantou" id="jiantou5"><img src="styles/images/gz_bj2.png" /></span>
                            </li>
                            <li id="gz_on6">
                                <a href="javascript:void(0)" onclick="showTable(6)" class="bor_no">资金规则</a>
                                <span class="jiantou" id="jiantou6" style="display:none"><img src="styles/images/gz_bj2.png" /></span>
                            </li>
                        </ul>
                    </div>
                    <div class="gz_blo1">
                        <!--交易规则-->
                        <div id="tableDiv5" style="display:;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="rule_table">
                        <tr class="top_title">
                        <th scope="col" width="28%">交易品种</th>
                        <th scope="col" width="24%">石蜡</th>
                        <th scope="col" width="24%">金工艺品</th>
                        <th scope="col" width="24%">银饰品</th>
                        </tr>
                        <tr>
                        <th scope="row">交易单位</th>
                        <td>千克</td>
                        <td>吨</td>
                        <td>吨</td>
                        </tr>
                        <tr>
                        <th scope="row">报价单位</th>
                        <td>元/千克</td>
                        <td>元/吨</td>
                        <td>元/吨</td>
                        </tr>

    <tr>
    <th scope="row">小数点位</th>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    </tr>
                        <tr>
                        <th scope="row">交易时间</th>
                        <td colspan="3">周一到周五09:00~04:00
                        </td>

                        </tr>

    <tr>
    <th scope="row">合约定金</th>
    <td colspan="3">20/100/1000</td>
    </tr>

    <tr>
    <th scope="row">止盈止损点</th>
    <td>6/9/15</td>
    <td>30/50/80</td>
    <td>4/6/10</td>
    </tr>

    <tr>
    <th scope="row">建仓成交手续费(根据止盈止损点配)</th>
    <td>15%/15%/15%</td>
    <td>15%/15%/15%</td>
    <td>15%/15%/15%</td>
    </tr>

    <tr>
    <th scope="row">持仓过夜</th>
    <td colspan="3">无</td>
    </tr>
    <tr>
    <th scope="row">下单冷却时间</th>
    <td colspan="3">30秒</td>
    </tr>
    <tr>
    <th scope="row">单品最大订购数量</th>
    <td colspan="3">6</td>
    </tr>
    <tr>
    <th scope="row">单品最大下单金额</th>
    <td colspan="3">5000元</td>
    </tr>
    <tr>
    <th scope="row">单品当日最高持仓金额</th>
    <td colspan="3">50000元</td>
    </tr>
    <tr>
    <th scope="row">当日现金累计最大亏损</th>
    <td colspan="3">2手</td>
    </tr>
    <tr>
    <th scope="row">结算平仓</th>
    <td colspan="3">由于不允许过夜，因此当客户持仓到结算时间未成交，则系统把合约定金全部返还</td>
    </tr>
                        </table>
                        </div>
                        <!--资金规则-->
                        <div id="tableDiv6" style="display:none;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="rule_table">
                            <tr class="top_title">
                            <th scope="col" width="30%">&nbsp;</th>
                            <th scope="col" width="35%">充值</th>
                            <th scope="col" width="35%">提现</th>
                            </tr>
                            <tr>
                            <th scope="row">单笔最低金额</th>
                            <td>20元</td>
                            <td>不限</td>
                            </tr>
                            <tr>
                            <th scope="row">单笔最高金额</th>
                            <td>3000元</td>
                            <td>不限</td>
                            </tr>
                            <tr>
                            <th scope="row">单日最大次数</th>
                            <td>不限</td>
                            <td>3次</td>
                            </tr>
                            <tr>
                            <th scope="row">单日最大金额</th>
                            <td colspan="2">20000元</td>
                            </tr>
                            <tr>
                            <th scope="row">申请时间</th>
                            <td colspan="2">09:00-03:00</td>
                            </tr>
                            <tr>
                            <th scope="row">入账时间</th>
                            <td colspan="2">T+0</td>
                            </tr>
                            <tr>
                            <th scope="row">可选银行</th>
                            <td colspan="2">支持银联的所有银行</td>
                            </tr>
                            <tr>
                            <th scope="row">安全保障</th>
                            <td colspan="2">充值、提现功能采用中国银联接口，
                            资金三方存管，存取更放心！</td>
                            </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
           
    </body>
</html>
