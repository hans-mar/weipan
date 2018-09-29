<?php common\assets\JqueryFormAsset::register($this) ?>
<?php use common\helpers\Html; ?>

<?php $form = self::beginForm(['id' => 'editUserForm']) ?>
<h2>用户授权</h2>
<table>
    <tr>
        <th>用户名</th>
        <td><?= $user['realname'] ?></td>
    </tr>
    <?php if ($roles): ?>
    <tr>
        <th>当前所有的角色</th>
        <td><?= $form->field($model, 'roles')->checkboxList($roles) ?></td>
    </tr>
    <?php endif ?>
</table>
<?= Html::submitInput('修改', ['id' => 'editUserSubmit']) ?>
<?php self::endForm() ?>

<script>
$(function () {
    // 删除权限多选框多余的默认隐藏域
    $("input[type='hidden'][name*='AuthItemChild[permissions]']:gt(0)").remove();
    // 表单提交
    $("#editUserSubmit").click(function () {
        $("#editUserForm").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (msg.state) {
                    var href = $.parent(".pagination li.active a").attr('href');
                    if (href) {
                        parent.$.fancybox.close();
                        parent.window.location.href = href;
                    } else {
                        parent.window.location.reload();
                    }
                } else {
                    $.alert(msg.info);
                }
            }
        }));

        return false;
    });
});
</script>