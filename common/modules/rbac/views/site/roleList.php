<?php use common\helpers\Html; ?>
<?php use common\helpers\Inflector; ?>
<?php use common\helpers\ArrayHelper; ?>

<?= $this->render('_tabMenu', compact('tabMenu')) ?>

<table>
    <tr>
        <th width="120">角色名称（规则）</th>
        <th>拥有的权限</th>
        <th width="65">操作</th>
    </tr>
    <?php foreach ($roles as $role): ?>
    <tr>
        <td>
            <?= $role->name ?>
            <?php if ($role->rule_name): ?>
                <br>( <?= Html::finishSpan($role->rule_name) ?> )
            <?php endif ?>
        </td>
        <td>
            <?php
            $childRoles = $childPermissions = [];
            foreach ($role->children as $child) {
                if (array_key_exists($child['child'], $roles)) {
                    $childRoles[] = $child;
                } else {
                    $childPermissions[] = $child;
                }
            }
            if ($childRoles) {
                echo Html::likeSpan('角色：') . '<br>';
                $d = '';
                foreach ($childRoles as $childRole) {
                    echo $d . $childRole['child'];
                    $d = '，';
                }
            }
            if ($childPermissions) {
                if ($childRoles) {
                    echo '<br>';
                }
                echo Html::warningSpan('权限：') . '<br>';
                ArrayHelper::multisort($childPermissions, 'child');
                $permissionGroup = [];
                foreach ($childPermissions as $childPermission) {
                    $controller = explode('-', Inflector::camel2id($childPermission->child))[1];
                    $permissionGroup[$controller][] = $childPermission;
                }
                foreach ($permissionGroup as $controller => $permissions) {
                    echo Html::successSpan($controller) . '<br>';
                    $d = '';
                    foreach ($permissions as $permission) {
                        echo $d . Html::span($permission->childItem['description'], ['data-key' => $permission->child]);
                        $d = '，';
                    }
                    echo '<br>';
                }
            }
            ?>
        </td>
        <td>
            <?= Html::a('编辑', ['site/updateRole', 'name' => $role->name], ['class' => 'edit-fancybox']) ?>
            <?= Html::a('删除', ['site/ajaxDeleteRole'], ['class' => 'deleteRole', 'data-name' => $role->name]) ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<script>
$(function() {
    $(".deleteRole").click(function () {
        var $a = $(this);
        $.confirm('确认删除？', function () {
            $.post($a.attr('href'), {name: $a.data('name')}, function (msg) {
                if (msg.state) {
                    $.alert(msg.info, function () {
                        $a.parents('tr').remove();
                    });
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        });
        return false;
    });
});
</script>