<?php use common\helpers\Hui; ?>
<style type="text/css">
.form-label {
    display: inline-block;
    width: auto;
}
.row {
    width: 260px;
    display: inline-block;
}
.input-text {
    width: 120px;
}
table {
    border-collapse: collapse;
}
tr td {
    text-align: center;
}
</style>
<h3 style="text-align: center"><?= $product->name ?></h3>

<?php $form = self::beginForm(['showLabel' => false]) ?>
<table width="70%">
    <tr>
        <td>保证金</td>
        <td>止盈止损点位</td>
        <td>手续费(%)</td>
        <td>最大手数</td>
        <td>操作</td>
    </tr>
<?php foreach ($models as $key => $model): ?>
    <tr class="trade-time-row">
        <td class="hidden"><?= $form->field($model, "[$key]product_id")->hiddenInput(['value' => $product->id]) ?></td>
        <td><?= $form->field($model, "[$key]deposit")->textInput(['placeholder' => $model->label('deposit')]) ?></td>
        <td><?= $form->field($model, "[$key]one_profit")->textInput(['placeholder' => $model->label('one_profit')]) ?></td>
        <td><?= $form->field($model, "[$key]fee")->textInput(['placeholder' => $model->label('fee')]) ?></td>
        <td><?= $form->field($model, "[$key]max_hand")->textInput(['placeholder' => $model->label('max_hand')]) ?></td>
        <td><?= Hui::dangerBtn('删除', null, ['class' => 'deleteBtn', 'data' => ['title' => '删除', 'id' => $model->id, 'url' => url(['deletePrice'])]]) ?></td>
    </tr>
<?php endforeach ?>
</table>
<input type="hidden" id="tradeTypeHidden" value="<?= $key + 1 ?>">
<?= Hui::successBtn('添加新一行', null, ['id' => 'addBtn']) ?>
<?= $form->submit() ?>
<?php self::endForm() ?>

<script>
$(function () {
    $("#addBtn").click(function () {
        $newItem = $(".trade-time-row:eq(0)").clone();
        $(".trade-time-row:last").after($newItem);
        var k = parseInt($("#tradeTypeHidden").val());
        $newItem.find('input.input-text').each(function () {
            var arr = $(this).attr('name').split('[0]');
            $(this).attr('name', arr[0] + '[' + k + ']' + arr[1]);
        });
        $newItem.find('td:last a').remove();
        $("#tradeTypeHidden").val(parseInt(k) + 1);
    });
    $(".deleteBtn").click(function () {
        var $this = $(this);
        $.post($(this).data('url'), {id: $(this).data('id')}, function (msg) {
            $.alert('删除成功', function () {
                location.reload();
            });
        });
    });
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (msg.state) {
                    $.alert(msg.info, function () {
                        window.parent.location.reload();
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