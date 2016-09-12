<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 9/11/2016
 * Time: 10:09 PM
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\LoginForm;
use api\modules\v1\models\SignUpForm;
use common\helpers\Helpers;
use common\models\Restaurants;
use Yii;
use common\models\User;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\MethodNotAllowedHttpException;
use yii\helpers\ArrayHelper;

class ClientController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['sign-up','log-in'],
                    'authMethods' => [
                        HttpBearerAuth::className(),
                    ],
                ],
            ]
        );
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['index']);
        unset($actions['options']);
        return $actions;
    }

    public function actionSignUp()
    {
        $post_data = Yii::$app->request->post();
        $sing_up_form = new SignUpForm();
        $sing_up_form->setAttributes($post_data);
        if (!$sing_up_form->validate()) {
            return Helpers::HttpException(422, 'validation failed', ['error' => $sing_up_form->errors]);
        }
        $user = User::findOne(['email' => $sing_up_form->email]);
        if (!$user) {
            $new_user = User::NewBasicSignUp($sing_up_form->username, $sing_up_form->email, $sing_up_form->password, User::CLIENT);
            if(!$new_user)
                return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
            return Helpers::formatResponse(true, 'sign up success', ['auth_key' => $new_user['auth_key']]);
        } else {//TODO if we add the email verification check this again.
            return Helpers::HttpException(422, 'validation failed', ['error' => 'this email already taken please try another.']);
        }
        return Helpers::HttpException(501,'not implemented', ['error' => 'Something went wrong, try again later or contact the admin.']);
    }

    public function actionLogIn()
    {
        $post_data = Yii::$app->request->post();
        $login_form = new LoginForm();
        $login_form->setAttributes($post_data);
        if (!$login_form->validate()) {
            return Helpers::HttpException(422, 'validation failed', ['error' => $login_form->errors]);
        }
        $user = User::Login($login_form->email, $login_form->password);
        if(!$user)
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
        return Helpers::formatResponse(true, 'log in success', ['auth_key' => $user['auth_key']]);
    }

    public function actionLogOut()
    {
        $headers = Yii::$app->getRequest()->getHeaders();

        $clientUser = User::findIdentityByAccessToken(explode(' ', $headers['authorization'])[1]);
        if (empty($clientUser))
            return Helpers::HttpException(404 ,'not found', ['error' => 'user not found']);

        $clientUser->auth_key = '';
        if(!$clientUser->save(false))
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);

        return Helpers::formatResponse(true, 'log out success', null);
    }

    public function actionGetRestaurants()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();
        
        if($request->isGet) {
            if(!isset($get_data['id']))
                return Restaurants::getRestaurants();
        }

        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function beforeAction($event)
    {
        $request_action = explode('/', Yii::$app->getRequest()->getUrl());
        $actions = [
            'sign-up' => ['POST'],
            'log-in' => ['POST'],
            'log-out' => ['POST'],
        ];

        foreach ($actions as $action => $verb) {
            if (in_array($action, $request_action)) {
                if (!in_array(Yii::$app->getRequest()->getMethod(), $actions[$action]))
                    throw new MethodNotAllowedHttpException("Method Not Allowed");
            }
        }
        return parent::beforeAction($event);
    }
}