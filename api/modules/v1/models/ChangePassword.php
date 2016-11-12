<?php

namespace api\modules\v1\models;


use yii\base\Model;
use yii\helpers\ArrayHelper;

class ChangePassword extends Model {

    public $old_password;
    public $new_password;
    public $new_password_confirm;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['old_password','new_password','new_password_confirm'], 'required'],
                [['old_password','new_password', 'new_password_confirm'], 'filter', 'filter' => 'trim'],
                ['new_password_confirm', 'compare', 'compareAttribute'=>'new_password', 'message'=>"passwords don't match" ]
            ]
        );
    }
}