<?php $form = self::beginForm() ?>
<?= $user->title('综会成员') ?>
<?= $form->field($user, 'username') ?>
<?= $form->field($user, 'realname') ?>
<?= $form->field($adminLeader, 'mobile') ?>
<?= $form->field($adminLeader, 'deposit') ?>
<?= $form->field($user, 'password')->textInput(['placeholder' => $user->isNewRecord ? '' : '不填表示不修改']) ?>
<?= $form->field($authItem, 'roles')->label('角色')->checkboxList($roles) ?>
<?= $form->submit($user) ?>
<?php self::endForm() ?>

<script>
$(function () {
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (msg.state) {
                    $.alert('操作成功', function () {
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