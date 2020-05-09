<?php
namespace kr0lik\compositeModel;

use yii\base\Model;
use yii\web\UploadedFile;

class CompositeResourceFabric
{
    /**
     * Create and load single composite model
     */
    public static function createSingle(string $compositeModelClass, string $resourceAttribute): Model
    {
        $compositeModel = CompositeModelFabric::createSingle($compositeModelClass);

        if ($newFile = UploadedFile::getInstance($compositeModel, $resourceAttribute)) {
            $compositeModel->$resourceAttribute = $newFile;
        }

        return $compositeModel;
    }

    /**
     * Create and load resource to a multiple composite model
     *
     * @return Model[] Array of composite models
     */
    public static function createMultiple(string $compositeModelClass, string $resourceAttribute): array
    {
        $models = CompositeModelFabric::createMultiple($compositeModelClass);

        foreach ($models as $i => $model) {
            if ($newFile = UploadedFile::getInstance($model, "[{$i}]$resourceAttribute")) {
                $model->$resourceAttribute = $newFile;
            }
        }

        return $models;
    }
}
