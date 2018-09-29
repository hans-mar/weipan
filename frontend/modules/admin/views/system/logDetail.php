<?php use common\helpers\ArrayHelper; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <style type="text/css">
/* reset */
html, body, div, span, h1, h2, h3, h4, h5, h6, p, pre, a, code, em, img, strong, b, i, ul, li{
    margin: 0;
    padding: 0;
    border: 0;
    font-size: 100%;
    font: inherit;
    vertical-align: baseline;
}
body {
    line-height: 1;
}
ul {
    list-style: none;
}

/* base */
a {
    text-decoration: none;
}
a:hover{
    text-decoration: underline;
}
h1, h2, h3, p, img, ul li{
    font-family: Arial,sans-serif;
    color: #505050;
}
/*corresponds to min-width of 860px for some elements (.header .footer .element ...)*/
@media screen and (min-width: 800px) {
    html,body{
        overflow-x: hidden;
    }
}

/* header */
.header {
    margin: 0 auto;
    background: #f3f3f3;
    padding: 20px 50px 30px 50px;
    border-bottom: #ccc 1px solid;
}
.header h1 {
    font-size: 30px;
    color: #e57373;
    margin-bottom: 30px;
}
.header h1 span, .header h1 span a {
    color: #e51717;
}
.header h1 a {
    color: #e57373;
}
.header h1 a:hover{
    color: #e51717;
}
.header img {
    float: right;
    margin-top: -15px;
}
.header h2 {
    font-size: 20px;
    line-height: 1.25;
}
/* call stack */
.call-stack {
    padding: 0 50px;
}
.call-stack ul li {
    padding: 12px 0;
    line-height: 26px;
}
.call-stack ul li:hover {
    background: #edf9ff;
}
    </style>
</head>

<body>
    <div class="header">
        <h1>
            <span>PHP <?= $log->getLevelValue() ?></span> –
            <a><?= $log->category ?></a>
        </h1>
        <h2><?= $reason ?></h2>
    </div>
    <div class="call-stack">
        <ul>
            <?php foreach ($message as $info): ?>
            <li>
                <span class="item-number"><?= $info ?></span>
            </li>
            <?php endforeach ?>
        </ul>
    </div>
</body>