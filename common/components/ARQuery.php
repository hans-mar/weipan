<?php

namespace common\components;
use common\models\AdminUser;

/**
 * ARModel查询的基类
 *
 * @author ChisWill
 */
class ARQuery extends \yii\db\ActiveQuery
{
    use \common\traits\QueryTrait;

    /**
     * 设置当前后台用户绑定的前台用户条件
     * ps.为确保条件的正确组装，建议将此方法放在最后进行调用
     * eg:$query->where('id>1')->orWhere('age<100')->children()->all();
     * 
     * @param  string|array $alias 关联前台用户表的别名
     * @return object
     */
    public function children($alias = 'user')
    {
        $admins = \common\models\AdminUser::getSubAdmins();

        if (is_array($alias)) {
            foreach ($alias as $subAlias) {
                $operator[] = ['in', $subAlias . '.admin_id', $admins];
            }
            array_unshift($operator, 'or');
            $this->andWhere($operator);
        } elseif (is_string($alias)) {
            $this->andWhere(['or', $alias . '.admin_id', $admins]);
        }

        return $this;
    }

    public function manager()
    {
        // user表
        switch (u()->power) {
            case AdminUser::POWER_LEADER:
                $idArr = AdminUser::find()->where(['pid' => u()->id])->map('id', 'id');
                $this->andWhere(['in', 'user.admin_id', $idArr]);
                break;
            case AdminUser::POWER_MANAGER:
                $this->andWhere(['user.admin_id' => u()->id]);
                break;
        }
        $arr = req('search');
        if (isset($arr['leader']) && !empty($arr['leader'])) {
            $adminUser = AdminUser::find()->where(['username' => $arr['leader']])->one();
            if (!empty($adminUser)) {
                $idArr = AdminUser::find()->where(['pid' => $adminUser->id])->map('id', 'id');
                $this->andWhere(['in', 'user.admin_id', $idArr]);
            }
        }
        return $this;
    }

    public function retail()
    {
        if (u()->power < AdminUser::POWER_ADMIN) {
            $this->andWhere(['adminUser.pid' => u()->id]);
        }
        return $this;
    }

    public function selfManager()
    {
        if (u()->power < AdminUser::POWER_ADMIN) {
            $this->andWhere(['adminUser.id' => u()->id]);
        }
        return $this;
    }

    /**
     * 与 common\widgets\Table 组件配套使用，可以自动增加关联表的搜索条件
     *
     * @param  string $name 搜索参数的name前缀值
     * @return object
     */
    public function andTableSearch($name = 'search')
    {
        foreach (get($name, []) as $key => $value) {
            if (strpos($key, '.') !== false) {
                list($alias, $field) = explode('.', $key);
                $column = $alias . '.' . $field;
                if (strpos($field, 'state') !== false || strpos($field, 'status') !== false) {
                    $this->andFilterWhere([$column => $value]);
                } else {
                    $this->andFilterWhere(['like', $column, $value]);
                }
            }
        }
        return $this;
    }
}
