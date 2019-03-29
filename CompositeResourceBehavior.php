<?php
namespace kr0lik\compositeModel;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class CompositeResourceBehavior extends Behavior
{
    public $attributes = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => '_save',
            ActiveRecord::EVENT_BEFORE_UPDATE => '_save',
            ActiveRecord::EVENT_BEFORE_DELETE => '_delete'
        ];
    }

    public function _delete($event)
    {
        $status = true;

        foreach ($this->attributes as $attribute) {
            if ($this->owner->$attribute) {
                if (is_array($this->owner->$attribute)) {
                    foreach ($this->owner->$attribute as $model) {
                        $model->trigger(ActiveRecord::EVENT_BEFORE_DELETE);
                        if ($model->hasErrors()) {
                            $status = false;
                        }
                    }
                } else {
                    $this->owner->$attribute->trigger(ActiveRecord::EVENT_BEFORE_DELETE);
                    if ($this->owner->$attribute->hasErrors()) {
                        $status = false;
                    }
                }
            }
        }

        return $status;
    }

    public function _save($event)
    {
        $status = true;

        foreach ($this->attributes as $attribute) {
            if ($this->owner->$attribute) {
                if (is_array($this->owner->$attribute)) {
                    foreach ($this->owner->$attribute as $model) {
                        $model->trigger(ActiveRecord::EVENT_BEFORE_INSERT);
                        if ($model->hasErrors()) {
                            $status = false;
                        }
                    }
                } else {
                    $this->owner->$attribute->trigger(ActiveRecord::EVENT_BEFORE_INSERT);
                    if ($this->owner->$attribute->hasErrors()) {
                        $status = false;
                    }
                }
            }
        }

        return $status;
    }
}
