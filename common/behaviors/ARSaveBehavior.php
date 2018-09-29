<?php

namespace common\behaviors;

use Yii;
use yii\db\BaseActiveRecord;

/**
 * SaveBehavior automatically fills the specified attributes with the current datetime and userId.
 *
 * @author ChisWill
 */
class ARSaveBehavior extends \yii\base\Behavior
{
    public $createdByAttribute = 'created_by';

    public $updatedByAttribute = 'updated_by';

    public $createdAttribute = 'created_at';

    public $updatedAttribute = 'updated_at';

    public $attributes = [];

    public $dateValue = null;

    public $uidValue = null;

    protected $map;

    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdAttribute, $this->createdByAttribute, $this->updatedAttribute, $this->updatedByAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => [$this->updatedAttribute, $this->updatedByAttribute]
            ];
        }

        if ($this->dateValue === null) {
            $this->dateValue = date('Y-m-d H:i:s');
        }

        if ($this->uidValue === null) {
            $this->uidValue = u('id') ?: 0;
        }

        $this->map = [
            $this->createdAttribute => $this->dateValue,
            $this->updatedAttribute => $this->dateValue,
            $this->createdByAttribute => $this->uidValue,
            $this->updatedByAttribute => $this->uidValue
        ];
    }

    public function events()
    {
        return array_fill_keys(array_keys($this->attributes), 'evaluateAttributes');
    }

    public function evaluateAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = $this->attributes[$event->name];
            foreach ($attributes as $attribute) {
                if (array_key_exists($attribute, $this->owner->getAttributes()) && !array_key_exists($attribute, $this->owner->getDirtyAttributes())) {
                    $this->owner->$attribute = $this->map[$attribute];
                }
            }
        }
    }
}
