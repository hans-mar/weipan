<?php common\assets\JqueryFormAsset::register($this) ?>
<?php $form = self::beginForm(['enctype' => 'multipart/form-data', 'id' => 'uploadForm']) ?>
<input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="cw" />
<input type="text" name="username">
<input type="file" name="Upload[uploadFile]">
<input type="button" id="uploadBtn" value="上传">
<?php self::endForm() ?>
<style type="text/css">
.progress{
    width:100%;
    border:1px solid #4da8fe;
    border-radius:40px;
    height:20px;
    position:relative;
}
.progress .label{
    position:relative;
    text-align:center;
}
.progress .bar{
    position:absolute;
    left:0;top:0;
    background:#4D90FE;
    height:20px;
    border-radius:40px;
    min-width:20px;
}
</style>
<div id="progress" class="progress" style="margin-bottom:15px;">
    <div class="bar" style="width:0%;"></div>
    <div class="label">0%</div>
</div>

<script type="text/javascript">
$(function () {
    $("#uploadBtn").click(function () {
        $("#uploadForm").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                tes(msg);
            }
        }));
        setTimeout(fetch_progress, 500);
    });
});
function fetch_progress(){
    $.get('process', function(data){
        var progress = parseInt(data);

        $('#progress .label').html(progress + '%');
        $('#progress .bar').css('width', progress + '%');

        if (progress < 100) {
            setTimeout('fetch_progress()', 100);
        } else {
            $('#progress .label').html('完成!');
        }
    }, 'html');
}
</script>