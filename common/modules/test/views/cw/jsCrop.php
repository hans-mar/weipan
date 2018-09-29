<?php common\assets\CropperAsset::register($this); ?>
<?php common\assets\LayerAsset::register($this); ?>

<?php $form = self::beginForm() ?>
<input type="hidden" value="" id="img" name="image">
<input type="file" id="file">
<input type="submit" value="submit" id="btn">
<?php self::endForm() ?>

<script>
$(function () {
    $("#btn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '操作成功', function () {
                        // parent.location.reload();
                        $("body").append($("<img>").attr('src', msg.info));
                    });
                } else {
                    $.alert(msg.info);
                }
            }
        }));
        return false;
    });

    // $('#img').cropper({
    //     aspectRatio: 16 / 9,
    //     crop: function(e) {
    //         // Output the result data for cropping image.
    //         console.log(e.x);
    //         console.log(e.y);
    //         console.log(e.width);
    //         console.log(e.height);
    //         console.log(e.rotate);
    //         console.log(e.scaleX);
    //         console.log(e.scaleY);
    //     }
    // });

    // var $image = $('img');
     
    //     $image.attr("src", '/1.jpg');    

    //     $image.on("load", function() {        // 等待图片加载成功后，才进行图片的裁剪功能
    //         $image.cropper({
    //             aspectRatio: 1 / 1　　// 1：1的比例进行裁剪，可以是任意比例，自己调整
    //         });
    //     })

    $("#file").change(function(event) {
        var file = this.files[0],
            reader = new FileReader(),
            image = new Image();
        var $this = $(this);
        
        reader.onload = function () {
            image.src = reader.result;
            var canvasdata = $(image).cropper("getCanvasData");
            var cropBoxData = $(image).cropper('getCropBoxData');
            
            convertToData(image.src, canvasdata, cropBoxData, function (base64src) {
                $("#img").val(base64src);
                $this.tips('就绪');
            });
        };
        reader.readAsDataURL(file);
    });

    // $(selector).on("tap", function() {
    //     var src = $image.eq(0).attr("src");
    //     var canvasdata = $image.cropper("getCanvasData");
    //     var cropBoxData = $image.cropper('getCropBoxData');
        
    //     convertToData(src, canvasdata, cropBoxData, function(basechar) {
    //         // 回调后的函数处理        
    //     });
    // })

    function convertToData(url, canvasdata, cropdata, callback) {
        var cropw = cropdata.width; // 剪切的宽
        var croph = cropdata.height; // 剪切的宽
        var imgw = canvasdata.width; // 图片缩放或则放大后的高
        var imgh = canvasdata.height; // 图片缩放或则放大后的高
        
        var poleft = canvasdata.left - cropdata.left; // canvas定位图片的左边位置
        var potop = canvasdata.top - cropdata.top; // canvas定位图片的上边位置
        
        var canvas = document.createElement("canvas");
        var ctx = canvas.getContext('2d');
        
        canvas.width = cropw;
        canvas.height = croph;
        
        var img = new Image();
        img.src = url;
        
        img.onload = function() {
            this.width = imgw;
            this.height = imgh;
                // 这里主要是懂得canvas与图片的裁剪之间的关系位置
            ctx.drawImage(this, poleft, potop, this.width, this.height);
            var base64 = canvas.toDataURL('image/jpg', 1);  // 这里的“1”是指的是处理图片的清晰度（0-1）之间，当然越小图片越模糊，处理后的图片大小也就越小
            callback && callback(base64)      // 回调base64字符串
        }
    } 
});
</script>