<?php \common\assets\JqueryFormAsset::register($this);?>    

<?php $form = self::beginForm(['id'=>'form']) ?>
<input type="text" name="tid" value="<?= mt_rand(10000,20000) ?>">
<input type="button" id="button">
<img src="data:image/gif;base64,R0lGODlhEAAQAPQAAP///5mZmfz8/NTU1OTk5MrKytDQ0PX19evr683NzeHh4d7e3vj4+Ojo6PLy8tfX19vb2wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH+FU1hZGUgYnkgQWpheExvYWQuaW5mbwAh+QQACgAAACH/C05FVFNDQVBFMi4wAwEAAAAsAAAAABAAEAAABVAgII5kaZ6lMBRsISqEYKqtmBTGkRo1gPAG2YiAW40EPAJphVCREIUBiYWijqwpLIBJWviiJGLwukiSkDiEqDUmHXiJNWsgPBMU8nkdxe+PQgAh+QQACgABACwAAAAAEAAQAAAFaCAgikfSjGgqGsXgqKhAJEV9wMDB1sUCCIyUgGVoFBIMwcAgQBEKTMCA8GNRR4MCQrTltlA1mCA8qjVVZFG2K+givqNnlDCoFq6ioY9BaxDPI0EACzxQNzAHPAkEgDAOWQY4Kg0JhyMhACH5BAAKAAIALAAAAAAQABAAAAVgICCOI/OQKNoUSCoKxFAUCS2khzHvM4EKOkPLMUu0SISC4QZILpgk2bF5AAgQvtHMBdhqCy6BV0RA3A5ZAKIwSAkWhSwwjkLUCo5rEErm7QxVPzV3AwR8JGsNXCkPDIshACH5BAAKAAMALAAAAAAQABAAAAVSICCOZGmegCCUAjEUxUCog0MeBqwXxmuLgpwBIULkYD8AgbcCvpAjRYI4ekJRWIBju22idgsSIqEg6cKjYIFghg1VRqYZctwZDqVw6ynzZv+AIQAh+QQACgAEACwAAAAAEAAQAAAFYCAgjmRpnqhADEUxEMLJGG1dGMe5GEiM0IbYKAcQigQ0AiDnKCwYpkYhYUgAWFOYCIFtNaS1AWJESLQGAKq5YWIsCo4lgHAzFmPEI7An+A3sIgc0NjdQJipYL4AojI0kIQAh+QQACgAFACwAAAAAEAAQAAAFXyAgjmRpnqhIFMVACKZANADCssZBIkmRCLCaoWAIPm6FBUkwJIgYjR5LN7INSCwHwYktdIMqgoNFGhQQpMMt0WCoiGDAAvkQMYkIGLCXQI8OQzdoCC8xBGYFXCmLjCYhADsAAAAAAAAAAAA=">
<?php self::endForm() ?>

<script type="text/javascript">
$(function(){
    $("#button").click(function () {
        // $.ajax({
        //     type:'post',
        //     async: false,
        //     data:{tid:123},
        //     success:function(msg){
        //         tes(msg)
        //     }
        // })
        $("#form").ajaxSubmit($.config('ajaxSubmit', {
                success: function(msg) {
                    tes(msg)
                }
            })
        );
    })
})
</script>