<?php
namespace kr0lik\compositeModel;

use yii\db\ActiveRecord;

trait CompositeModelTrait
{
    public $isNewRecord = true;

    private $oldAttributes = [];

    public function __construct(array $config = [])
    {
        if ($config) {
            $this->oldAttributes = $config;
            $this->isNewRecord = false;
        }

        return parent::__construct($config);
    }

    public function getOldAttributes(): array
    {
        return $this->oldAttributes;
    }

    public function save()
    {
        $this->trigger(ActiveRecord::EVENT_BEFORE_INSERT);
    }

    public function delete()
    {
        $this->trigger(ActiveRecord::EVENT_BEFORE_DELETE);
    }
}
