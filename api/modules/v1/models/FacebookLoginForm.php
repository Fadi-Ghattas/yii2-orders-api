<?php

namespace api\modules\v1\models;


use yii\base\Model;
use yii\helpers\ArrayHelper;

class FacebookLoginForm extends Model
{
    public $full_name;
    public $email;
    public $facebook_id;
    public $access_token;
    public $picture;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['full_name', 'email', 'facebook_id', 'access_token' , 'picture'], 'required'],
                ['email', 'email'],
                ['email', 'filter', 'filter' => 'trim'],
            ]
        );
    }
}