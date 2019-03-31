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
            ActiveRecord::EVENT_BEFORE_INSERT => '_saveCompositeModel',
            ActiveRecord::EVENT_BEFORE_UPDATE => '_saveCompositeModel',
            ActiveRecord::EVENT_AFTER_UPDATE => '_clearCompositeModel',
            ActiveRecord::EVENT_AFTER_DELETE => '_deleteCompositeModel'
        ];
    }

    public function _deleteCompositeModel($event)
    {
        $status = true;

        foreach ($this->attributes as $attribute) {
            if ($this->owner->$attribute) {
                if (is_array($this->owner->$attribute)) {
                    foreach ($this->owner->$attribute as $model) {
                        $model->trigger(ActiveRecord::EVENT_AFTER_DELETE);
                        if ($model->hasErrors()) {
                            $status = false;
                        }
                    }
                } else {
                    $this->owner->$attribute->trigger(ActiveRecord::EVENT_AFTER_DELETE);
                    if ($this->owner->$attribute->hasErrors()) {
                        $status = false;
                    }
                }
            }
        }

        return $status;
    }

    public function _saveCompositeModel($event)
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
    
    public function _clearCompositeModel($event)
    {
        $status = true;

        foreach ($this->attributes as $attribute) {
            if ($this->owner->$attribute) {
                if (is_array($this->owner->$attribute)) {
                    foreach ($this->owner->$attribute as $model) {
                        $model->trigger(ActiveRecord::EVENT_AFTER_UPDATE);
                        if ($model->hasErrors()) {
                            $status = false;
                        }
                    }
                } else {
                    $this->owner->$attribute->trigger(ActiveRecord::EVENT_AFTER_UPDATE);
                    if ($this->owner->$attribute->hasErrors()) {
                        $status = false;
                    }
                }
            }
        }

        return $status;
    }
}
