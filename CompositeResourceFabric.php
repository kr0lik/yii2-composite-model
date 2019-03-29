<?php
namespace kr0lik\compositeModel;

use Yii;
use yii\web\UploadedFile;
use yii\base\Model;

class CompositeResourceFabric
{
    /**
     * Create and load single composite model
     *
     * @param string $compositeModelClass
     * @param string $compositeModelAttribute
     * @return Model
     */
    public static function createSingle(string $compositeModelClass, string $compositeModelAttribute): Model
    {
        $compositeModel = new $compositeModelClass;
        $compositeModel->isNewRecord = false;

        $compositeModel->load(Yii::$app->request->post());

        if ($newFile = UploadedFile::getInstance(new $compositeModel, $compositeModelAttribute)) {
            $compositeModel->$compositeModelAttribute = $newFile;
        }

        return $compositeModel;
    }

    /**
     * Create and load resource to a multiple composite model
     *
     * @param string $compositeModelClass
     * @param string $compositeModelAttribute
     * @return array Array of composite models
     */
    public static function createMultiple(string $compositeModelClass, string $compositeModelAttribute): array
    {
        $post = Yii::$app->request->post((new $compositeModelClass)->formName(), []);

        $models = [];
        foreach ($post as $i => $data) {
            $newCompositeModel = new $compositeModelClass($data);
            $newCompositeModel->isNewRecord = false;

            if ($newFile = UploadedFile::getInstance($newCompositeModel, "[{$i}]$compositeModelAttribute")) {
                $newCompositeModel->$compositeModelAttribute = $newFile;
            }

            $models[$i] = $newCompositeModel;
        }

        return $models;
    }
}
