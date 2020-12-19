<?php

namespace kr0lik\compositeModel;

use yii\base\Model;

class CompositeModelFactory
{
    public static function create(string $compositeModelClass): Model
    {
        return new $compositeModelClass;
    }
}