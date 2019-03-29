<?php
namespace kr0lik\compositeModel;

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
}
