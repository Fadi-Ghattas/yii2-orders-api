<?php

namespace api\modules\v1\models;


use yii\base\Model;
use yii\helpers\ArrayHelper;

class ChangePassword extends Model {

    public $new_password;
    public $new_password_confirm;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['new_password','new_password_confirm'], 'required'],
                [['new_password', 'new_password_confirm'], 'filter', 'filter' => 'trim'],
                ['new_password_confirm', 'compare', 'compareAttribute'=>'new_password', 'message'=>"passwords don't match" ]
            ]
        );
    }
}