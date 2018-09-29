<?php use common\helpers\Hui; ?>
<h2 style="text-align: center"><?= $model->name ?></h2>
<h5 style="text-align: center; color: red;">开始和时间必须一起填写才会生效；可以通过清除开始或结束时间来消除一个时间段</h5>
<?php $form = self::beginForm() ?>
<?php foreach ($time as $key => $value): ?>
    <div class="trade-time-row">
    <?= $form->field($model, 'trade_start_time[]')->datepicker(['value' => $value['start'], 'placeholder' => '开始时间', 'fmt' => 'HH:mm'])->label('交易时间段 <span class="tradeTimeSpan">' . ($key + 1) . '</span>') ?>
    <?= $form->field($model, 'trade_end_time[]')->datepicker(['value' => $value['end'], 'placeholder' => '截止时间', 'fmt' => 'HH:mm'])->label('') ?>
    </div>
<?php endforeach ?>
<input type="hidden" id="tradeTypeHidden" value="<?= $key + 1 ?>">
<?= Hui::successBtn('添加新一行', null, ['id' => 'addBtn']) ?>
<?= $form->submit() ?>
<?php self::endForm() ?>

<script>
$(function () {
    $("#addBtn").click(function () {
        $div = $(".trade-time-row:eq(0)").clone();
        $(".trade-time-row:last").after($div);
        var k = $("#tradeTypeHidden").val();
        $(".tradeTimeSpan:eq(" + k +")").html(parseInt(k) + 1);
        $("#tradeTypeHidden").val(parseInt(k) + 1);
    });
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '操作成功', function () {
                        parent.location.reload();
                    });
                } else {
                    $.alert(msg.info);
                }
            }
        }));
        return false;
    });
});
</script>