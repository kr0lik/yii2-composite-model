<?php
namespace kr0lik\compositeModel;

use yii\base\Behavior;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * @property Model $owner
 */
class CompositeModelBehavior extends Behavior
{
    public $attributes = [];

    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => '_validateCompositeModel',
            ActiveRecord::EVENT_BEFORE_INSERT => '_saveCompositeModel',
            ActiveRecord::EVENT_BEFORE_UPDATE => '_saveCompositeModel',
            ActiveRecord::EVENT_AFTER_UPDATE => '_clearCompositeModel',
            ActiveRecord::EVENT_AFTER_DELETE => '_deleteCompositeModel',
        ];
    }

    public function _validateCompositeModel($event)
    {
        $status = true;

        $validator = new CompositeModelValidator();
        foreach ($this->attributes as $attribute) {
            $validator->validateAttribute($this->owner, $attribute);
        }

        return !$this->owner->hasErrors();
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
