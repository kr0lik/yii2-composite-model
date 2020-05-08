<?php
namespace kr0lik\compositeModel;

use yii\validators\Validator;
use yii\base\Model;

class CompositeModelValidator extends Validator
{
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

    public function validateAttribute($model, $attribute)
    {
        if ($model->$attribute) {
            if (is_array($model->$attribute)) {
                foreach ($model->$attribute as $i => $compositeModel) {
                    if ($compositeModel instanceof Model) {
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
            } elseif ($model->$attribute instanceof Model) {
                $model->$attribute->validate();

                if ($this->function) {
                    $function = $this->function;
                    $function($model->$attribute);
                }

                if ($model->$attribute->hasErrors()) {
                    $this->addError($model, $attribute, $this->message);
                }
            }
        }
    }
}
