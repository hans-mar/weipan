
// 弹框代码
function openNew() {
    //获取页面的高度和宽度
    var sWidth = document.body.scrollWidth;
    var sHeight = document.body.scrollHeight;

    //获取页面的可视区域高度和宽度
    var wHeight = document.documentElement.clientHeight;

    var oMask = document.createElement("div");
    oMask.id = "mask";
    oMask.style.height = sHeight + "px";
    oMask.style.width = sWidth + "px";
    document.body.appendChild(oMask);
    var oLogin = document.createElement("div");
    oLogin.id = "login";
    oLogin.innerHTML = "<div class='loginCon'><div id='createorderbox'> <div class='createorder-content'> <div class='createchoose-wrap'>建仓看涨</div> <div class='key-value boxflex'><div id='definecashnum' class='box_flex_1'></div> </div> <div class='key-value boxflex'> <label class='key'>合约定金:</label> <div class='box_flex_1' id='setting-point'> <ul class='table'><li class='active'>10</li><li>100</li><li>200</li></ul> </div> </div> <div class='key-value boxflex'> <label class='key'>数量:</label> <div class='box_flex_1 num-wrap'> <span class='btn-coin btn-minute' data-value='-1'>-</span> <input type='tel' value='1' onpaste='return false' oncontextmenu='return false' oncopy='return false' oncut='return false'> <span class='btn-coin btn-add' data-value='1'>+</span> </div> </div> <div class='key-value boxflex'> <label class='key'>止盈/止损点:</label> <div class='box_flex_1' id='setting-point'> <ul class='table'><li class='active'>5</li><li>7</li><li>10</li></ul> </div> </div> <div class='sure-btn-wrap'> <div class='table'> <div class='table-cell cancel' id='close' > <label>取消</label></div> <div class='table-cell determine'> <label>确定</label></div> </div> <p class='ptipstorage'>收盘时对于未成交订单将自动平仓，合约定金全额返还</p> <p>交易时间：周一~周五9:00~1:00 每日4:30~7:00休市结算</p> </div> </div> </div></div>"; 
    document.body.appendChild(oLogin);


    //设置登陆框的left和top
    oLogin.style.left = 0;
    oLogin.style.bottom = 0;
    //点击关闭按钮
    var oClose = document.getElementById("close");

    //点击登陆框以外的区域也可以关闭登陆框
    oClose.onclick = oMask.onclick = function() {
        document.body.removeChild(oLogin);
        document.body.removeChild(oMask);
    }

};

// 弹框代码
function openNew1() {
    //获取页面的高度和宽度
    var sWidth = document.body.scrollWidth;
    var sHeight = document.body.scrollHeight;

    //获取页面的可视区域高度和宽度
    var wHeight = document.documentElement.clientHeight;

    var oMask = document.createElement("div");
    oMask.id = "mask";
    oMask.style.height = sHeight + "px";
    oMask.style.width = sWidth + "px";
    document.body.appendChild(oMask);
    var oLogin = document.createElement("div");
    oLogin.id = "login";
    oLogin.innerHTML = "<div class='loginCon'><div id='createorderbox'> <div class='createorder-content'> <div class='createchoose-wrap' style='background-color: #0c9a0f;'>建仓看跌</div> <div class='key-value boxflex'><div id='definecashnum' class='box_flex_1'></div> </div> <div class='key-value boxflex'> <label class='key'>合约定金:</label> <div class='box_flex_1' id='setting-point'> <ul class='table'><li class='active'>10</li><li>100</li><li>200</li></ul> </div> </div> <div class='key-value boxflex'> <label class='key'>数量:</label> <div class='box_flex_1 num-wrap'> <span class='btn-coin btn-minute' data-value='-1'>-</span> <input type='tel' value='1' onpaste='return false' oncontextmenu='return false' oncopy='return false' oncut='return false'> <span class='btn-coin btn-add' data-value='1'>+</span> </div> </div> <div class='key-value boxflex'> <label class='key'>止盈/止损点:</label> <div class='box_flex_1' id='setting-point'> <ul class='table'><li class='active'>5</li><li>7</li><li>10</li></ul> </div> </div> <div class='sure-btn-wrap'> <div class='table'> <div class='table-cell cancel' id='close' > <label>取消</label></div> <div class='table-cell determine'> <label style='    background-color: #0c9a0f;'>确定</label></div> </div> <p class='ptipstorage'>收盘时对于未成交订单将自动平仓，合约定金全额返还</p> <p>交易时间：周一~周五9:00~1:00 每日4:30~7:00休市结算</p> </div> </div> </div></div>"; 
    document.body.appendChild(oLogin);


    //设置登陆框的left和top
    oLogin.style.left = 0;
    oLogin.style.bottom = 0;
    //点击关闭按钮
    var oClose = document.getElementById("close");

    //点击登陆框以外的区域也可以关闭登陆框
    oClose.onclick = oMask.onclick = function() {
        document.body.removeChild(oLogin);
        document.body.removeChild(oMask);
    }
};

window.onload = function() {
    var oBtn = document.getElementById("btnLogin");
    var oBtn1 = document.getElementById("btnLogin1");
    //点击看跌按钮
  
    oBtn.onclick = function() {
        openNew();
        return false;
    }
    oBtn1.onclick = function() {
        openNew1();
        return false;
    }

}











