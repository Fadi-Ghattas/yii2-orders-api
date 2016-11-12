<?php

namespace api\modules\v1\models;



use yii\base\Model;
use yii\helpers\ArrayHelper;
use common\models\User;

class SignUpForm extends Model
{
    public $full_name;
    public $email;
    public $password;
    public $status;
    public $phone_number;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['full_name', 'email', 'password', 'phone_number'], 'required'],
                ['status', 'default', 'value' => User::STATUS_ACTIVE],
                ['status', 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_DELETED]],
                ['email', 'email'],
                ['email', 'filter', 'filter' => 'trim'],
            ]);
    }
}