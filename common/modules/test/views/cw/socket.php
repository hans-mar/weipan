<?php \common\assets\SocketIOAsset::register($this) ?>

<input type="text" id="msg">

<input type="button" id="login" value="login">

<input type="button" id="view" value="view">

<input type="button" id="chat" value="chat">

<div id="response"></div>

<script type="text/javascript">
$(function () {
    var io = $.io();
    // var io = io('http://115.28.93.101:30000');
    
    io.on("connect", function() {

        $("#login").click(function () {
            io.emit('login', $("#msg").val());
        });

        $("#view").click(function () {
            io.emit('view');
        });

        $("#chat").click(function () {
            io.emit('chat', $("#msg").val());
        });

        io.on('response', function (msg) {
            $("#response").append(msg + '<br>');
        });
    });

    io.on('disconnect', function () {
        location.reload();
    });
});
</script>