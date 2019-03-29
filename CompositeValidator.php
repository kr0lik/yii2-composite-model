<?php
namespace kr0lik\compositeModel;

use yii\validators\Validator;
use yii\base\Model;

class CompositeValidator extends Validator
{
    public $message = 'Копозитный тип содержит ошибки.';

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
                            $model->addError("{$attribute}{$i}", $this->message);
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
                    $model->addError($attribute, $this->message);
                }
            }
        }
    }
}
