<?php use common\helpers\Hui; ?>
<?php common\assets\SwitchAsset::register($this) ?>

<div class="mb-20">
    <h3 style="display: inline-block; vertical-align: bottom;" >风险控制总阀：</h3>
    <div class="switch size-M" id="switch" data-on="danger" data-off="success">
        <input type="checkbox" <?= $switch ? 'checked' : '' ?> />
    </div>
</div>

<div id="riskArea" <?php if (!$switch): ?>style="display: none;"<?php endif ?>>
    <table class="table table-border table-hover">
        <?php foreach ($products as $product): ?>
        <tr>
            <th>
                <h5><?= $product['name'] ?></h5>
            </th>
            <td class="text-r">
                <div class="switch size-M productSwitch" data-on="warning" data-off="success">
                    <input name="product[<?= $product['table_name'] ?>]" type="checkbox" class="product" <?= !empty($risk_product[$product['table_name']]) ? 'checked' : '' ?>>
                </div>
            </td>
        </tr>
        <?php endforeach ?>
        <tr>
            <td colspan="2" class="text-c"><?= Hui::secondaryBtn('保存', null, ['id' => 'submitBtn', 'class' => 'size-L']) ?></td>
        </tr>
    </table>
</div>

<script>
$(function () {
    $('#switch').on('switch-change', function (e, data) {
        var value = data.value;
        if (value) {
            $("#riskArea").slideDown(400);
        } else {
            $("#riskArea").slideUp(400);
            $.post('', {risk_switch: 0});
        }
    });
    $("#submitBtn").click(function () {
        var names = {risk_switch : 1};
        $(".product").each(function () {
            names[$(this).attr('name')] = $(this)[0].checked ? 1 : 0;
        });
        $.post('', names, function (msg) {
            $.alert('保存成功');
        });
        return false;
    });
});
</script>