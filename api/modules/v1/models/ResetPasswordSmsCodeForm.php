<?php

namespace api\modules\v1\models;


use yii\base\Model;
use yii\helpers\ArrayHelper;

class ResetPasswordSmsCodeForm extends Model {

    public $phone_number;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['phone_number'], 'required'],
                ['phone_number', 'filter', 'filter' => 'trim'],
            ]
        );
    }
}