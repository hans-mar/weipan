<?php

namespace common\models;

use Yii;

/**
 * 这是表 `menu` 的模型
 */
class Menu extends \common\components\ARModel
{
    const CATEGORY_DEFAULT = 1;

    const IS_SHOW_YES = 1;
    const IS_SHOW_NO = -1;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['pid', 'level', 'sort', 'is_show', 'category', 'state', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 30],
            [['url'], 'string', 'max' => 250]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'name' => '菜单名',
            'pid' => '父ID',
            'level' => '层级',
            'sort' => '排序值',
            'url' => '跳转链接',
            'is_show' => '是否显示',
            'category' => '菜单分类',
            'state' => '状态',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /****************************** 以下为设置关联模型的方法 ******************************/

    // public function getRelation()
    // {
    //     return $this->hasOne(Class::className(), ['id' => 'target_id']);
    // }

    /****************************** 以下为公共显示条件的方法 ******************************/

    public function search()
    {
        $this->setSearchParams();

        return self::find()
            ->filterWhere([
                'menu.id' => $this->id,
                'menu.pid' => $this->pid,
                'menu.level' => $this->level,
                'menu.sort' => $this->sort,
                'menu.is_show' => $this->is_show,
                'menu.category' => $this->category,
                'menu.state' => $this->state,
                'menu.created_by' => $this->created_by,
                'menu.updated_by' => $this->updated_by,
            ])
            ->andFilterWhere(['like', 'menu.name', $this->name])
            ->andFilterWhere(['like', 'menu.url', $this->url])
            ->andFilterWhere(['like', 'menu.created_at', $this->created_at])
            ->andFilterWhere(['like', 'menu.updated_at', $this->updated_at])
            ->andTableSearch()
        ;
    }

    /****************************** 以下为公共操作的方法 ******************************/

    /**
     * 创建菜单，使用该方法前确保已经对 `name` 和 `pid` 进行赋值
     *
     * @param  integer $category 菜单分类的常量值
     * @return boolean
     */
    public function createMenu($category = self::CATEGORY_DEFAULT)
    {
        if ($this->pid !== '0') {
            $parentMenu = self::findOne($this->pid);
            $this->level = $parentMenu->level + 1;
        } else {
            $this->level = 1;
        }
        $this->category = $category;

        $count = self::find()->where('level = :level AND pid = :pid', [':level' => $this->level, ':pid' => $this->pid])->count();
        if (empty($parentMenu)) {
            $this->code = $count + 1 . '';
        } else {
            $this->code = $parentMenu->code . '-' . ($count + 1);
        }
        
        if ($this->save()) {
            if ($this->pid !== '0') {
                $parentMenu->updateCounters(['child_num' => 1]);
            }
            $this->sort = $this->id;
            $this->update();

            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除当前菜单，以及其子类
     * @return boolean
     */
    public function deleteMenu()
    {
        if ($this->delete()) {
            $parent = static::findOne($this->pid);
            if ($parent) {
                $parent->updateCounters(['child_num' => -1]);
            }
            if ($this->code) {
                self::deleteAll('LOCATE("' . $this->code . '", `code`, 1) = 1');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取指定分类的菜单
     * 
     * @param  integer $category 分类的ID
     * @return array             分类数据
     */
    public static function getMenuData($category = self::CATEGORY_DEFAULT)
    {
        return static::find()->where('category = ' . $category)->orderBy('level ASC, sort ASC')->all();
    }

    /****************************** 以下为字段的映射方法和格式化方法 ******************************/

    // Map method of field `is_show`
    public static function getIsShowMap($prepend = false)
    {
        $map = [
            self::IS_SHOW_YES => '显示',
            self::IS_SHOW_NO => '隐藏',
        ];

        return self::resetMap($map, $prepend);
    }

    // Format method of field `is_show`
    public function getIsShowValue($value = null)
    {
        return $this->resetValue($value);
    }
}