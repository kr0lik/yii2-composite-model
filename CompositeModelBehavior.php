<?php
namespace kr0lik\compositeModel;

use yii\base\Behavior;
use yii\base\Event;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * @property Model $owner
 */
class CompositeModelBehavior extends Behavior
{
    /**
     * @var string[]
     */
    public $attributes = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => '_triggerEvent',
            ActiveRecord::EVENT_AFTER_INSERT => '_triggerEvent',
            ActiveRecord::EVENT_BEFORE_UPDATE => '_triggerEvent',
            ActiveRecord::EVENT_AFTER_UPDATE => '_triggerEvent',
            ActiveRecord::EVENT_BEFORE_DELETE => '_triggerEvent',
            ActiveRecord::EVENT_AFTER_DELETE => '_triggerEvent',
        ];
    }

    public function _triggerEvent(Event $event): bool
    {
        $status = true;

        foreach ($this->attributes as $attribute) {
            if ($this->owner->$attribute) {
                if (is_array($this->owner->$attribute)) {
                    /** @var Model $model */
                    foreach ($this->owner->$attribute as $model) {
                        $model->trigger($event->name);
                        if ($model->hasErrors()) {
                            $status = false;
                        }
                    }
                } else {
                    /** @var Model $model */
                    $model = $this->owner->$attribute;
                    $model->trigger($event->name);
                    if ($model->hasErrors()) {
                        $status = false;
                    }
                }
            }
        }

        return $status;
    }
}
