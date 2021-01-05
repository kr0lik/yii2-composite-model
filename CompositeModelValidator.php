<?php
namespace kr0lik\compositeModel;

use yii\validators\Validator;
use yii\base\Model;

class CompositeModelValidator extends Validator
{
    /**
     * @var string
     */
    public $message = '{attribute} содержит ошибки.';

    /**
     * Add check for composite model
     *
     * @var \Closure
     * params: $model
     *
     * use $model->addError() method in function;
     */
    public $function;

    /**
     * @param Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute): void
    {
        if ($model->$attribute) {
            if (is_array($model->$attribute)) {
                foreach ($model->$attribute as $i => $compositeModel) {
                    if ($compositeModel instanceof Model) {
                        $this->validateCompositeModel($model, $attribute, $compositeModel);
                    }
                }
            } else {
                $compositeModel = $model->$attribute;
                if ($compositeModel instanceof Model) {
                    $this->validateCompositeModel($model, $attribute, $compositeModel);
                }
            }
        }
    }

    private function validateCompositeModel(Model $model, string $attribute, Model $compositeModel): void
    {
        $compositeModel->validate();

        if ($this->function) {
            $function = $this->function;
            $function($compositeModel);
        }

        if ($compositeModel->hasErrors()) {
            $this->addError($model, $attribute, $this->message);
        }
    }
}
