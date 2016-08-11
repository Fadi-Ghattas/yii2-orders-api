<?php
/**
 * Created by PhpStorm.
 * User: Ahmad
 * Date: 12/06/2016
 * Time: 11:19 AM
 */

namespace api\modules\v1\models;

use common\models\Country;
use common\models\Emirate;
use common\models\Rate;
use Yii;

class User extends \common\models\User
{
    public $password;

    public function login()
    {
    }

    public function rules()
    {
        $rules = parent::rules();

        $rules[] = ['email', 'filter', 'filter' => 'trim'];
        $rules[] = ['email', 'required'];
        $rules[] = ['email', 'email'];
        $rules[] = ['email', 'unique'];
//            , 'when' => function ($model, $attribute) {
//            return $model->{$attribute} !== $model->getOldAttribute($attribute);
//        },
//            'on' => 'update_scenario'
//            , 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'];
//        $rules[] = ['email', 'unique', 'on' => 'create_scenario', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'];

        //$rules[] = ['full_name', 'required'];
        //$rules[] = ['full_name', 'string', 'min' => 2, 'max' => 255];
        //$rules[] = [['first_name', 'last_name', 'password', 'mobile', 'gender'], 'required'];
        //$rules[] = [['first_name', 'last_name'], 'string', 'max' => 50];
        $rules[] = ['password', 'string', 'min' => 6];
        //$rules[] = ['mobile', 'udokmeci\yii2PhoneValidator\PhoneValidator'];
        //$rules[] = ['gender', 'string', 'max' => 10];

        return $rules;
    }


    //Specify the returned fields for each scenario
    public function fields()
    {
        return [
            'id',
            'username',
            'email',
            'auth_key'
        ];
    }

    //Override attributes function to add password attribute to the model
    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes[] = 'password';
        //$attributes[] = 'interests';
        return $attributes;
    }


    public function beforeSave($insert)
    {

        if ($this->isNewRecord) {
            $request = Yii::$app->request;
            $password = $request->getBodyParam('password');
            $username = $request->getBodyParam('username');


            $this->username = $username;
            $this->setPassword($password);
            $this->generateAuthKey();

        }

        return parent::beforeSave($insert);
    }


    public function afterSave($insert, $changedAttributes)
    {
//        $auth = Yii::$app->authManager;
//        $role = $auth->getRole('user');
//        $roles = Yii::$app->authManager->getRolesByUser($this->id);
//        if (!isset($roles["user"])) {
//            $auth->assign($role, $this->id);
//        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return parent::validatePassword($password);
    }


}