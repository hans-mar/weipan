<?php

namespace common\modules\rbac\rules;

use Yii;

/**
 * 检查当前用户是否是普通用户组
 *
 * @author ChisWill
 */
class UserGroupRule extends \yii\rbac\Rule
{
    public $name = 'userGroup';

    /**
     * @param  string|integer $user   用户 ID.
     * @param  Item           $item   该规则相关的角色或者权限
     * @param  array          $params 传给 ManagerInterface::checkAccess() 的参数
     * @return boolean               代表该规则相关的角色或者权限是否被允许
     */
    public function execute($user, $item, $params = [])
    {
        return $user > \common\models\User::START_ID;
    }
}
