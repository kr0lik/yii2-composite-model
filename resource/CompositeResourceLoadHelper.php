<?php
namespace kr0lik\compositeModel\resource;

use kr0lik\compositeModel\CompositeModelLoadHelper;
use yii\base\Model;
use yii\web\UploadedFile;

class CompositeResourceLoadHelper
{
    /**
     * Create and load single composite model
     *
     * @param array<string, mixed> $data Data to be loaded
     */
    public static function createAndLoadModel(string $compositeModelClass, array $data, string $resourceAttribute = 'resource'): ?Model
    {
        $compositeModel = CompositeModelLoadHelper::createAndLoadModel($compositeModelClass, $data);

        if (null === $compositeModel) {
            return null;
        }

        if ($newFile = UploadedFile::getInstance($compositeModel, $resourceAttribute)) {
            $compositeModel->setAttributes([$resourceAttribute => $newFile]);
        }

        return $compositeModel;
    }

    /**
     * Create and load resource to a multiple composite model
     *
     * @param array<string, mixed> $data Data to be loaded
     *
     * @return Model[] Array of composite models
     */
    public static function createAndLoadModels(string $compositeModelClass, array $data, string $resourceAttribute = 'resource'): array
    {
        $models = CompositeModelLoadHelper::createAndLoadModels($compositeModelClass, $data);

        foreach ($models as $i => $model) {
            if ($newFile = UploadedFile::getInstance($model, "[{$i}]$resourceAttribute")) {
                $model->setAttributes([$resourceAttribute => $newFile]);
            }
        }

        return $models;
    }
}
