<?php if (isset($tabMenu)): ?>
<ul class="rbac-tab clearfix">
<?php foreach ($tabMenu as $action => $name): ?>
    <?php
    if (substr($this->context->action->id, strrpos($this->context->action->id, '/')) == $action) {
        $class = 'cur';
    } else if ($this->context->action->id == 'update-role' && $action == 'role-list') {
        $class = 'cur';
    } else {
        $class = '';
    } ?>
    <li class="<?= $class ?>"><a href="<?= self::createUrl(['site/' . $action]) ?>"><?= $name ?></a></li>
<?php endforeach ?>
</ul>
<?php endif ?>