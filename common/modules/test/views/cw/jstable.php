<?php yii\bootstrap\BootstrapAsset::register($this) ?>
<?php common\assets\JsPaginationAsset::register($this) ?>

<?= $html ?>
<?= $html2 ?>

<script>
$(function() {
    // $("#t1").paginate(8);
    $(".table").paginate();
});
</script>