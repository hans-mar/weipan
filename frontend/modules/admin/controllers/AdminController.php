<?php

namespace admin\controllers;

use Yii;
use common\helpers\Hui;
use common\helpers\Html;
use common\helpers\Inflector;
use common\helpers\FileHelper;
use common\helpers\ArrayHelper;
use admin\models\AdminMenu;
use admin\models\AdminUser;
use admin\models\AdminLeader;
use admin\models\Retail;
use common\modules\rbac\models\AuthItem;

/**
 * @author ChisWill
 */
class AdminController extends \admin\components\Controller
{
    /**
     * @authname 管理员列表
     */
    public function actionList()
    {
        $query = (new AdminUser)->search()
            ->andWhere(['state' => AdminUser::STATE_VALID])
            ->andWhere(['<=', 'power', u()->power]);
        $html = $query->getTable([
            'id' => ['search' => true],
            'username' => ['search' => true],
            'realname' => ['search' => true, 'type' => 'text'],
            'roles' => ['header' => '角色', 'value' => function ($user) {
                $roles = [];
                foreach ($user->roles as $role) {
                    $roles[] = Html::likeSpan($role->item_name);
                }
                return implode('，', $roles);
            }],
            'state' => ['search' => 'select'],
            u()->power <= AdminUser::POWER_ADMIN ?:['type' => ['edit' => 'saveAdmin', 'delete' => 'ajaxDeleteAdmin']]
        ], [
            'addBtn' => ['saveAdmin' => '添加管理员']
        ]);

        return $this->render('list', compact('html'));
    }

    /**
     * @authname 创建/修改管理员
     */
    public function actionSaveAdmin($id = null)
    {
        $user = AdminUser::findModel($id);
        $this->checkAccess($user);
        $user->tmpPassword = $user->password;
        // 避免在页面上显示密码
        $user->password = null;
        // 获取当前的所有角色
        $roles = AuthItem::getRoleQuery()->map('name', 'name');
        // 填充当前用户拥有的角色
        $authItem = new AuthItem;
        $authItem->roles = AdminUser::roles($id);

        if ($user->load()) {
            if ($user->saveAdmin()) {
                return self::success();
            } else {
                return self::error($user);
            }
        }

        return $this->render('saveAdmin', compact('user', 'authItem', 'roles'));
    }

    /**
     * @authname 删除管理员
     */
    public function actionAjaxDeleteAdmin()
    {
        $id = post('id');
        $this->checkAccess(AdminUser::findModel($id));

        if ($id != u('id')) {
            return parent::actionDelete();
        } else {
            return self::error('不能删除自己');
        }
    }
    /**
     * @authname 综会列表
     */
    public function actionLeaderList()
    {
        $query = (new AdminUser)->search()
            ->joinWith(['leader'])
            ->andWhere(['adminUser.state' => AdminUser::STATE_VALID, 'adminUser.power' => AdminUser::POWER_LEADER]);

        $html = $query->getTable([
            'id' => ['search' => true],
            'username' => ['search' => true],
            'realname' => ['search' => true, 'type' => 'text'],
            'leader.mobile' => ['search' => true, 'type' => 'text'],
            'leader.deposit' => ['type' => 'text'],
            'leader.point' => ['header' => '返点%'],
            'state' => ['search' => 'select'],
            ['type' => ['delete'], 'width' => '250px', 'value' => function ($row) {
                return Hui::primaryBtn('修改返点', ['editPoint', 'id' => $row->id], ['class' => 'editBtn']);
            }]
        ], [
            'addBtn' => ['saveLeader' => '添加综会成员']
        ]);
      

        return $this->render('leaderList', compact('html'));
    }


    /**
     * @authname 修改综会返点%
     */
    public function actionEditPoint() 
    {
        $admin = AdminUser::findOne(get('id'));
        if (empty($admin)) {
            return error('查无此用户！');
        }
        if ($admin->power == AdminUser::POWER_LEADER) {
            $aminLeader = AdminLeader::findOne($admin->id);
            $aminLeader->point = post('point');
            $retail = Retail::find()->joinWith(['adminUser'])->where(['pid' => $admin->id])->orderBy('point DESC')->one();
            $point = 0;
            if (!empty($retail)) {
                $point = $retail->point;
            }
            if (is_int($aminLeader->point) || $aminLeader->point < 0 || $aminLeader->point > 100) {
                return error('综会的返点不能大于100%(设置返点为正整数)');
            }
            if ($aminLeader->point < $point) {
                return error('综会的返点不能小于他下面代理商的返点('.$point.')！');
            }
            $aminLeader->update(false);
            return success();
        } else {
           return error('非法参数！'); 
        }
    }

    /**
     * @authname 创建/修改综会
     */
    public function actionSaveLeader($id = null)
    {
        $user = AdminUser::findModel($id);
        $this->checkAccess($user);
        $user->tmpPassword = $user->password;
        // 避免在页面上显示密码
        $user->password = null;
        // 获取当前的所有角色
        $roles = AuthItem::getRoleQuery()->map('name', 'name');
        // 填充当前用户拥有的角色
        $authItem = new AuthItem;
        $authItem->roles = AdminUser::roles($id);
        //综会信息
        $adminLeader = new AdminLeader;
        $type='9998';//设定综会级别。

        if ($user->load() && $adminLeader->load()) {
            $user->pid = 2;
            if ($user->saveAdmin($type)) {
                $adminLeader->admin_id = $user->id;
                $adminLeader->save();
                return self::success();
            } else {
                return self::error($user);
            }
        }

        return $this->render('saveLeader', compact('user', 'authItem', 'roles', 'adminLeader'));
    }

    /**
     * @authname 角色列表
     */
    public function actionRoleList()
    {
        $query = AuthItem::getRoleQuery();
        $roles = $query->all();
        $categoryMap = AdminMenu::categoryMap();
        $html = $query->getTable([
            'name' => ['header' => '角色名称（规则）', 'width' => '15%', 'value' => function ($role) {
                $html = $role->name;
                if ($role->rule_name) {
                    $html .= '<br>（' . Html::finishSpan($role->rule_name) . '）';
                }
                return $html;
            }],
            ['header' => '拥有的权限', 'value' => function ($role) use ($roles, $categoryMap) {
                $childRoles = $childPermissions = [];
                $html = '';
                foreach ($role->children as $child) {
                    if (array_key_exists($child['child'], $roles)) {
                        $childRoles[] = $child;
                    } else {
                        $childPermissions[] = $child;
                    }
                }
                if ($childRoles) {
                    $html .= Html::likeSpan('角色') . '：';
                    $d = '';
                    foreach ($childRoles as $childRole) {
                        $html .= $d . $childRole['child'];
                        $d = '，';
                    }
                }
                if ($childPermissions) {
                    if ($childRoles) {
                        $html .= '<br>';
                    }
                    ArrayHelper::multisort($childPermissions, 'child');
                    $permissionGroup = [];
                    foreach ($childPermissions as $childPermission) {
                        $controller = explode('-', Inflector::camel2id($childPermission->child))[1];
                        $permissionGroup[$controller][] = $childPermission;
                    }
                    foreach ($permissionGroup as $controller => $permissions) {
                        $html .= Html::successSpan(ArrayHelper::getValue($categoryMap, $controller, '常规')) . '：';
                        $d = '';
                        foreach ($permissions as $permission) {
                            $html .= $d . Html::span($permission->childItem['description'], ['data-key' => $permission->child]);
                            $d = '，';
                        }
                        $html .= '<br>';
                    }
                }
                return $html;
            }],
            ['type' => ['edit' => 'editRole', 'delete' => 'ajaxDeleteRole']]
        ], [
            'addBtn' => ['createRole' => '创建角色']
        ]);

        return $this->render('roleList', compact('html'));
    }

    /**
     * @authname 创建角色
     */
    public function actionCreateRole()
    {
        $title = '创建角色';
        // 获取权限对象
        $auth = Yii::$app->authManager;
        // 获取当前的所有角色
        $roles = AuthItem::getRoleQuery()->map('name', 'name');
        // 获取当前的所有权限
        $permissions = AuthItem::getGroupPermissionData();
        //var_dump($permissions);
        //exit;
        // 获取模型
        $model = new AuthItem(['scenario' => 'createRole']);

        if ($model->load()) {
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
                return self::success();
            } else {
                return self::error($model);
            }
        }

        return $this->render('role', compact('title', 'roles', 'permissions', 'model'));
    }

    /**
     * @authname 编辑角色
     */
    public function actionEditRole($id)
    {
        $name = $id;
        $title = '编辑角色';
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

        if ($model->load()) {
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
                return self::success();
            } else {
                return self::error($model);
            }
        }

        return $this->render('role', compact('title', 'roles', 'permissions', 'model'));
    }

    /**
     * @authname 查看角色权限
     */
    public function actionAjaxRoleInfo()
    {
        $roleList = get('roleList');
        $auth = Yii::$app->authManager;
        $roles = [];
        foreach ($roleList as $key => $role) {
            $roles = array_merge($roles, array_keys($auth->getPermissionsByRole($role)));
        }
        $roles = array_unique($roles);

        return self::success($roles);
    }

    /**
     * @authname 删除角色
     */
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

    /**
     * @authname 权限列表
     */
    public function actionPermissionList()
    {
        $query = AuthItem::getAuthItemQuery();
        $categoryMap = AdminMenu::categoryMap();
        $html = $query->getTable([
            'name' => ['header' => '动作', 'value' => function ($item) {
                $name = Inflector::camel2id($item->name);
                $namePieces = explode('-', $name);
                unset($namePieces[0], $namePieces[1]);
                return lcfirst(Inflector::id2camel(implode('-', $namePieces)));
            }],
            'description' => ['type' => 'text', 'value' => function ($item) {
                return Hui::textInput(null, $item->description);
            }],
            'rule_name' => ['type' => 'text', 'value' => function ($item) {
                return Hui::textInput(null, $item->rule_name, ['placeholder' => '规则名或是规则类名']);
            }]
        ], [
            'beforeRow' => function ($item) use (&$controllerName, $categoryMap) {
                $name = Inflector::camel2id($item->name);
                $namePieces = explode('-', $name);
                if ($controllerName != $namePieces[1]) {
                    $controllerName = $namePieces[1];
                    return Html::tag('tr', Html::tag('th', ArrayHelper::getValue($categoryMap, $controllerName, '常规'), ['colspan' => 3, 'class' => 'text-c']));
                }
            },
            'isSort' => false,
            'paging' => false,
            'addBtn' => ['addPermission' => '添加权限'],
            'ajaxUpdateAction' => 'ajaxUpdatePermission'
        ]);

        return $this->render('permissionList', compact('html'));
    }

    /**
     * @authname 添加权限
     */
    public function actionAddPermission()
    {
        // 获取已经保存的权限信息
        $permissionMap = AuthItem::getPermissionQuery()->map('name', 'description');
        $models = [];
        // 获取文件中所有权限
        $actions = AuthItem::getFileActionList('admin', ['site']);
        foreach ($actions as $action => $description) {
            // 过滤已经保存的权限
            if (!array_key_exists($action, $permissionMap)) {
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
                return self::success();
            } else {
                $errors = [];
                foreach ($models as $key => $model) {
                    if ($model->hasErrors()) {
                        $index = $key + 1;
                        $errors[] = "第{$index}行，" . current($model->getFirstErrors());
                    }
                }
                return self::error($errors);
            }
        } else {
            $i = -1;
            $html = self::getTable($models, [
                ['header' => '序号', 'value' => function () use (&$i) {
                    return ++$i + 1;
                }],
                'name' => ['header' => '动作', 'value' => function ($model) use (&$i, &$action) {
                    $namePieces = explode('-', Inflector::camel2id($model->name));
                    unset($namePieces[0]);
                    return array_shift($namePieces) . '：' . ($action = lcfirst(Inflector::id2camel(implode('-', $namePieces)))) .
                           Html::hiddenInput("AuthItem[$i][name]", $model->name);
                }],
                'description' => ['header' => '描述', 'value' => function ($model) use (&$i, &$action) {
                    return Hui::textInput("AuthItem[$i][description]", $model->description ?: $action);
                }],
                'rule_name' => ['header' => '规则', 'value' => function ($model) use (&$i) {
                    return Hui::textInput("AuthItem[$i][rule_name]", $model->rule_name, ['placeholder' => '规则名或是规则类名']);
                }]
            ], [
                'ajaxLayout' => '{items}'
            ]);
        }

        return $this->render('addPermission', compact('html'));
    }

    /**
     * @authname 修改权限
     */
    public function actionAjaxUpdatePermission()
    {
        $auth = Yii::$app->authManager;
        $params = post('params');

        try {
            $authItem = AuthItem::findOne($params['key']);
            $authItem->$params['field'] = $params['value'];
            if ($authItem->validate()) {
                $permission = $auth->createPermission($params['key']);
                $permission->ruleName = $authItem->rule_name;
                $permission->description = $authItem->description;
                $auth->update($params['key'], $permission);
                return self::success();
            } else {
                return self::error($authItem);
            }
        } catch (\Exception $e) {
            throwex($e);
        }
    }

    private function checkAccess($user)
    {
        if ($user->power > u()->power) {
            throwex('你不能对其操作！');
        }
    }
}
