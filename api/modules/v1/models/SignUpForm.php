<?php

namespace api\modules\v1\models;



use yii\base\Model;
use yii\helpers\ArrayHelper;
use common\models\User;

class SignUpForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $role;
    public $status;

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['username', 'email', 'password'], 'required'],
                ['status', 'default', 'value' => User::STATUS_ACTIVE],
                ['status', 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_DELETED]],
                ['role', 'default', 'value' => User::CLIENT],
//                [['email'], 'unique'],
                ['email', 'email'],
                ['email', 'filter', 'filter' => 'trim'],
            ]);
    }
}