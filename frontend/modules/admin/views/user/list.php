<?php use common\helpers\Hui; ?>
<?php use common\helpers\Html; ?>

<?php if (u()->can('user/sendCoupon')): ?>
<div class="cl pd-5 bg-1 bk-gray mt-20">
    <span>赠送体验券：</span>
    <div class="select-box inline">
        <?= Html::dropDownList('coupon_id', null , admin\models\Coupon::find()->map('id', 'desc'), ['class' => 'search-map btn radius size-S', 'prompt' => '选择体验券']) ?>
        <?= Hui::input('number', 'number', 1) ?>
    </div>
    <?= Hui::primaryBtn('赠送', null, ['class' => 'size-M', 'id' => 'submitBtn']) ?>
</div>
<?php endif ?>

<?= $html ?>

<?php if (u()->isSuper()): ?>
<p class="cl pd-5 mt-20">
    <span>截止<?= self::$date ?>，共有<?= Html::redSpan($count) ?>个会员完成注册，交易数量已达<?= Html::redSpan($hand) ?>手，所有账户余额累计<?= Html::redSpan($amount) ?>元</span>
</p>
<a class="userExcel btn btn-success radius r">导出用户记录</a>
<?php endif ?>

<script>
$(function () {
    $("#submitBtn").click(function () {
        var ids = [], cfm = true;
        $("[name='selection[]']:checked").each(function(index, el) {
            ids.push($(this).val());
        });
        if (!$("[name='coupon_id']").val()) {
            $.alert('请选择一种体验券');
            return false;
        }
        if (ids.length === 0) {
            cfm = confirm('一个用户都不选择，将会送给所有用户，确定继续执行？');
        } else {
            cfm = confirm('确定赠送给选中用户？');
        }
        if (cfm) {
            $.post('<?= url(["sendCoupon"]) ?>', {ids: ids, coupon_id: $("[name='coupon_id']").val(), number: $("[name='number']").val()}, function (msg) {
                $.alert(msg.info);
            }, 'json');
        }
        return false;
    });

    $(".userExcel").on('click', function () {
        var str = '';
        $('.search-form ul>li').each(function(){
            var $this = $(this).find('.input-text');
            if ($this.attr('name') != undefined) {
                var value = $this.val();
                if (value.length > 0) {          
                    str += $this.attr('name') + '=' + value + '&';
                }
            }
        });
        var url = "<?= url(['user/userExcel?']) ?>" + str;
        window.location.href = url;
    });

    $(".list-container").on('click', '.editBtn', function () {
        var $this = $(this);
        $.prompt('请输入修改的密码', function (value) {
            $.post($this.attr('href'), {password: value}, function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '修改成功');
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        });
        return false;
    });
    $(".list-container").on('click', '.moveBtn', function () {
        var $this = $(this);
        $.prompt('请输入要修改的代理商id', function (value) {
            $.post($this.attr('href'), {admin_id: value}, function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '修改成功');
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        });
        return false;
    });

    $(".list-container").on('click', '.deleteBtn', function () {
        var $this = $(this);
        $.get($(this).attr('href'), function (msg) {
            if (msg.state) {
                $.alert(msg.info, function () {
                    location.reload();
                });
            } else {
                $.alert(msg.info);
            }
        });
        return false;
    });
});
</script>