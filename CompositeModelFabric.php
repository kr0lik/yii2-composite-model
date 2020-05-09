<?php
namespace kr0lik\compositeModel;

use Yii;
use yii\base\Model;

class CompositeModelFabric
{
    /**
     * Create and load single composite model
     */
    public static function createSingle(string $compositeModelClass): Model
    {
        $compositeModel = new $compositeModelClass;
        $compositeModel->isNewRecord = false;

        $compositeModel->load(Yii::$app->request->post());

        return $compositeModel;
    }

    /**
     * Create and load resource to a multiple composite model
     *
     * @return Model[] Array of composite models
     */
    public static function createMultiple(string $compositeModelClass): array
    {
        $post = Yii::$app->request->post((new $compositeModelClass)->formName(), []);

        $models = [];
        foreach ($post as $i => $data) {
            $newCompositeModel = new $compositeModelClass($data);
            $newCompositeModel->isNewRecord = false;

            $models[$i] = $newCompositeModel;
        }

        return $models;
    }
}
