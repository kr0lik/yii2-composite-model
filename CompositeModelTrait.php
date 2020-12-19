<?php
namespace kr0lik\compositeModel;

use yii\db\ActiveRecord;

trait CompositeModelTrait
{
    /**
     * @var bool
     */
    public $isNewRecord = true;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        if ($config) {
            $this->isNewRecord = false;
        }

        return parent::__construct($config);
    }
}
