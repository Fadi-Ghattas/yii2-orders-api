<?php

namespace api\modules\v1\models;


use yii\base\Model;
use yii\helpers\ArrayHelper;

class LoginForm extends Model
{
    public $email;
    public $password;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['email', 'password'], 'required'],
                ['email', 'email'],
                ['email', 'filter', 'filter' => 'trim'],
            ]
        );
    }
}