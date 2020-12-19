<?php
namespace kr0lik\compositeModel;

use Yii;
use yii\base\Model;

class CompositeModelLoadHelper
{
    /**
     * Create and load single composite model
     *
     * @param array<string, mixed> $data Data to be loaded
     */
    public static function createAndLoadModel(string $compositeModelClass, array $data): ?Model
    {
        $compositeModel = CompositeModelFactory::create($compositeModelClass);
        $compositeModel->isNewRecord = false;

        if (false === $compositeModel->load($data)) {
            return null;
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
    public static function createAndLoadModels(string $compositeModelClass, array $data): array
    {
        $model = CompositeModelFactory::create($compositeModelClass);
        $formName = $model->formName();
        unset($model);

        if (isset($data[$formName])) {
            return [];
        }

        $models = [];
        foreach ($data[$formName] as $i => $row) {
            if (!is_int($i)) {
                continue;
            }

            $compositeModel = CompositeModelFactory::create($compositeModelClass);
            $compositeModel->isNewRecord = false;

            if (true === $compositeModel->load($row, '')) {
                $models[$i] = $compositeModel;
            }
        }

        return array_values($models);
    }
}
