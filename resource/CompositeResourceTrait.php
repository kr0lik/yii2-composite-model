<?php

namespace kr0lik\compositeModel\resource;

use yii\db\ActiveRecord;

trait CompositeResourceTrait
{
    public function getResource(): string
    {
        $resourceName = $this->resource;

        if (! $resourceName) return '';

        $absoluteResourcePath = $this->getResourcePath('resource', true);

        if (! file_exists($absoluteResourcePath)) {
            return '';
        }

        return $this->getResourcePath('resource');
    }

    public function getName(): string
    {
        return $this->name ?: strtoupper(pathinfo($this->file, PATHINFO_EXTENSION));
    }

    public function getSize(): int
    {
        if (! $this->resource) return 0;

        $path = $this->getResourcePath('resource', true);

        if (! file_exists($path)) {
            return 0;
        }

        return filesize($path);
    }

    public function save(): void
    {
        $this->trigger(ActiveRecord::EVENT_BEFORE_INSERT);
        $this->trigger(ActiveRecord::EVENT_AFTER_INSERT);
    }

    public function delete(): void
    {
        $this->trigger(ActiveRecord::EVENT_BEFORE_DELETE);
        $this->trigger(ActiveRecord::EVENT_AFTER_DELETE);
    }
}