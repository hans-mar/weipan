<?php

namespace common\modules\rbac\controllers;

use Yii;
use common\models\AdminUser;
use common\helpers\FileHelper;
use common\helpers\ArrayHelper;
use common\modules\rbac\models\AuthItem;

/**
 * @author ChisWill
 */
class SiteController extends \common\components\WebController
{
    public $layout = 'main';

    public $permissionTabMenu = [
        'edit-permission-list' => '已创建权限',
        'create-permission-list' => '待创建权限'
    ];

    public $roleTableMenu = [
        'role-list' => '角色列表',
        'create-role' => '创建角色'
    ];

    public function init()
    {
        parent::init();

        $this->view->title = '权限管理 - ChisWill';
    }

    public function actionIndex()
    {
        return $this->redirect(['editPermissionList']);
    }

    public function actionCreatePermissionList()
    {
        $tabMenu = $this->permissionTabMenu;

        // 获取已经保存的权限信息
        $permissionQuery = AuthItem::getPermissionQuery();
        $map = $permissionQuery->map('name', 'description');
        $models = [];
        // 获取文件中所有权限
        $actions = AuthItem::getFileActionList();
        foreach ($actions as $action => $description) {
            // 过滤已经保存的权限
            if (!array_key_exists($action, $map)) {
                $model = new AuthItem;
                $model->name = $action;
                $model->description = $description;
                $models[] = $model;
            }
        }

        if (req()->isPost) {
            if (AuthItem::loadMultiple($models, post()) && AuthItem::validateMultiple($models)) {
                $auth = Yii::$app->authManager;
                foreach ($models as $index => $model) {
                    $permission = $auth->createPermission($model->name);
                    $permission->description = $model->description;
                    $permission->ruleName = $model->rule_name;
                    $auth->add($permission);
                }
                return $this->redirect(['editPermissionList']);
            }
        }

        return $this->render('createPermissionList', compact('tabMenu', 'map', 'actions', 'models'));
    }

    public function actionEditPermissionList()
    {
        $tabMenu = $this->permissionTabMenu;
        // 获取所有已经保存的权限
        $authItems = AuthItem::getCurrentAuthItems();

        if (req()->isPost) {
            if (AuthItem::loadMultiple($authItems, post()) && AuthItem::validateMultiple($authItems)) {
                $auth = Yii::$app->authManager;
                foreach ($authItems as $index => $item) {
                    $permission = $auth->createPermission($item->name);
                    $permission->description = $item->description;
                    $permission->ruleName = $item->rule_name;
                    $auth->update($item->name, $permission);
                }
                return $this->redirect(['editPermissionList']);
            }
        }

        return $this->render('editPermissionList', compact('tabMenu', 'authItems'));
    }

    public function actionRoleList()
    {
        $tabMenu = $this->roleTableMenu;

        $roles = AuthItem::getRoleQuery()->all();

        return $this->render('roleList', compact('tabMenu', 'roles'));
    }

    public function actionCreateRole()
    {
        $tabMenu = $this->roleTableMenu;
        // 获取权限对象
        $auth = Yii::$app->authManager;
        // 获取当前的所有角色
        $roles = AuthItem::getRoleQuery()->map('name', 'name');
        // 获取当前的所有权限
        $permissions = AuthItem::getGroupPermissionData();
        // 获取模型
        $model = new AuthItem(['scenario' => 'createRole']);

        if (req()->isPost) {
            $model->load();
            $model->description = FileHelper::getCurrentApp();
            if ($model->validate()) {
                $post = post('AuthItem', []);
                // 添加角色
                $role = $auth->createRole($model->name);
                $role->description = $model->description;
                $role->ruleName = $model->rule_name;
                $auth->add($role);
                // 获取角色和权限
                $roles = ArrayHelper::getValue($post, 'roles') ?: [];
                // 添加子角色
                foreach ($roles as $roleName) {
                    $childRole = $auth->getRole($roleName);
                    $auth->addChild($role, $childRole);
                }
                $permissions = ArrayHelper::getValue($post, 'permissions') ?: [];
                // 添加权限
                foreach ($permissions as $permissionName) {
                    $permission = $auth->getPermission($permissionName);
                    $auth->addChild($role, $permission);
                }
                return $this->redirect(['roleList']);
            }
        }

        return $this->render('createRole', compact('tabMenu', 'roles', 'permissions', 'model'));
    }

    public function actionUpdateRole($name)
    {
        $tabMenu = $this->roleTableMenu;

        // 获取权限对象
        $auth = Yii::$app->authManager;
        // 获取当前的所有角色
        $roles = AuthItem::getRoleQuery()->map('name', 'name');
        // 获取当前的所有权限
        $permissions = AuthItem::getGroupPermissionData();
        // 获取当前的角色
        $role = $auth->getRole($name);
        if (!$role) {
            throw new \yii\base\InvalidParamException('不存在该角色！');
        }
        // 获取模型
        $model = new AuthItem;
        $model->name = $model->oldRoleName = $role->name;
        $model->description = $role->description;
        $model->rule_name = $role->ruleName;
        $model->scenario = 'updateRole';
        $children = $auth->getChildren($role->name);
        foreach ($children as $child) {
            if ($child->type == AuthItem::TYPE_ROLE) {
                $model->roles[] = $child->name;
            } elseif ($child->type == AuthItem::TYPE_PERMISSION) {
                $model->permissions[] = $child->name;
            }
        }
        // 过滤掉不能添加为子集的角色
        AuthItem::filterLoopRoles($roles, $role);

        if (req()->isPost) {
            $model->load();
            if ($model->validate()) {
                $post = post('AuthItem', []);
                // 更改角色名
                $role->name = $model->name;
                $role->ruleName = $model->rule_name;
                $auth->update($name, $role);
                // 更改子角色以及权限
                $items = ['role', 'permission'];
                $methods = ['add', 'remove'];
                foreach ($items as $item) {
                    $postName = $item . 's';
                    $updateItems = ArrayHelper::getValue($post, $postName) ?: [];
                    list($add, $remove) = ArrayHelper::diff($model->$postName, $updateItems);
                    foreach ($methods as $method) {
                        foreach ($$method as $itemName) {
                            $getMethod = 'get' . ucfirst($item);
                            $authItem = $auth->$getMethod($itemName);
                            $updateMethod = $method . 'Child';
                            $auth->$updateMethod($role, $authItem);
                        }
                    }
                }
                return $this->redirect(['roleList']);
            }
        }

        return $this->render('createRole', compact('tabMenu', 'roles', 'permissions', 'model'));
    }

    public function actionAjaxDeleteRole()
    {
        $name = post('name');

        $auth = Yii::$app->authManager;

        $role = $auth->getRole($name);

        if ($role && $auth->remove($role)) {
            return self::success('删除成功！');
        } else {
            return self::error('删除失败！');
        }
    }

    public function actionUserList()
    {
        $query = (new AdminUser)->search()->with('roles.item')->orderBy('adminUser.id ASC')->asArray();
        $html = $query->getTable([
            'id' => ['search' => true],
            'realname' => ['search' => true],
            [
                'header' => '角色', 
                'value' => function ($user) {
                    $html = '';
                    foreach ($user['roles'] as $role) {
                        if ($role['item']['description'] === FileHelper::getCurrentApp()) {
                            $html .= '，' . $role['item_name'];
                        }
                    }
                    return trim($html, '，');
                }
            ],
            ['type' => ['edit' => 'editUser']]
        ]);

        return $this->render('userList', compact('html'));
    }

    public function actionEditUser($id)
    {
        $this->layout = 'empty';
        self::offEvent();
        // 获取所选用户
        $user = AdminUser::find()->with('roles.item')->where(['id' => $id])->asArray()->one();
        // 获取当前的所有角色
        $roles = AuthItem::getRoleQuery()->map('name', 'name');
        // 获取模型
        $model = new AuthItem;
        foreach ($user['roles'] as $role) {
            if ($role['item']['description'] === FileHelper::getCurrentApp()) {
                $model->roles[] = $role['item_name'];
            }
        }

        if (req()->isPost) {
            // 获取权限对象
            $auth = Yii::$app->authManager;
            // 获取表单提交数据
            $post = post('AuthItem', ['roles' => []]);
            $roles = $post['roles'] ?: [];
            list($add, $remove) = ArrayHelper::diff($model->roles, $roles);
            foreach ($add as $roleName) {
                $role = $auth->getRole($roleName);
                $auth->assign($role, $user['id']);
            }
            foreach ($remove as $roleName) {
                $role = $auth->getRole($roleName);
                $auth->revoke($role, $user['id']);
            }

            return self::success();
        }

        return $this->render('editUser', compact('user', 'roles', 'model'));
    }
}
