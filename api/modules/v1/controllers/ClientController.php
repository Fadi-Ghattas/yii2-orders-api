<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 9/11/2016
 * Time: 10:09 PM
 */

namespace api\modules\v1\controllers;




use Yii;
use api\modules\v1\models\LoginForm;
use api\modules\v1\models\SignUpForm;
use api\modules\v1\models\FacebookLoginForm;
use common\models\User;
use common\helpers\Helpers;
use common\models\Cuisines;
use common\models\MenuItems;
use common\models\Restaurants;
use common\models\Addresses;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\MethodNotAllowedHttpException;
use yii\helpers\ArrayHelper;

class ClientController extends ActiveController
{
    public $modelClass = '';

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['sign-up', 'log-in', 'facebook-login', 'restaurants', 'menu-items', 'cuisines'],
                    'authMethods' => [
                        HttpBearerAuth::className(),
                    ],
                ],
            ]
        );
    }

//    public function actions()
//    {
//        $actions = parent::actions();
//        unset($actions['create']);
//        unset($actions['update']);
//        unset($actions['delete']);
//        unset($actions['view']);
//        unset($actions['index']);
//        unset($actions['options']);
//        return $actions;
//    }

    public function actionSignUp()
    {
        $post_data = Yii::$app->request->post();
        $sing_up_form = new SignUpForm();
        $sing_up_form->setAttributes($post_data);
        if (!$sing_up_form->validate()) {
            return Helpers::HttpException(422, 'validation failed', ['error' => $sing_up_form->errors]);
        }
        $user = User::findOne(['email' => $sing_up_form->email]);
        //if account already exists
        if ($user && $user->source == User::SOURCE_BASIC) {
            return Helpers::HttpException(422, 'validation failed', ['error' => 'Account already exists']);
        }
        //if user connect("logged in") with facebook then he sing up again with the sing up form
        if ($user && $user->source == User::SOURCE_FACEBOOK) {
            return Helpers::HttpException(422, 'validation failed', ['error' => 'You are already sing up with facebook , you can login with facebook or reset your password and log in.']);
        }
        if (!$user) {
            $new_user = User::NewBasicSignUp($sing_up_form->full_name, $sing_up_form->email, $sing_up_form->phone_number, $sing_up_form->password, User::CLIENT);
            if(!$new_user)
                return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later']);
            return Helpers::formatResponse(true, 'sign up success', ['auth_key' => $new_user['auth_key']]);
        }
        return Helpers::HttpException(501,'not implemented', ['error' => 'Something went wrong, try again later or contact the admin.']);
    }

    public function actionLogIn()
    {
        $post_data = Yii::$app->request->post();
        if(!isset($post_data['source']))
            return Helpers::HttpException(422, 'validation failed', ['error' => "source is required"]);
        if(empty($post_data['source']))
            return Helpers::HttpException(422, 'validation failed', ['error' => "source can't be blank"]);

        if($post_data['source'] == User::SOURCE_BASIC) {
            $login_form = new LoginForm();
            $login_form->setAttributes($post_data);
            if (!$login_form->validate()) {
                return Helpers::HttpException(422, 'validation failed', ['error' => $login_form->errors]);
            }
            $user = User::Login($login_form->email, $login_form->password);
            if (!$user)
                return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
            return Helpers::formatResponse(true, 'log in success', ['auth_key' => $user['auth_key']]);
        }
        else if ($post_data['source'] == User::SOURCE_FACEBOOK)
        {
            $fb_login_form = new FacebookLoginForm();
            $fb_login_form->setAttributes($post_data);

            if (!$fb_login_form->validate()) {
                return Helpers::HttpException(422, 'validation failed', ['error' => $fb_login_form->errors]);
            }

            $user = User::findOne(['email' => $fb_login_form->email]);

            $fb_verify = User::VerifyFB($fb_login_form->email, $fb_login_form->facebook_id, $fb_login_form->access_token);
            if(!$fb_verify)
                return Helpers::HttpException(422, 'facebook validation failed', ['error' => 'facebook id or email verification failed']);

            if (!$user) {
                $new_user = User::NewFacebookSignUp($fb_login_form->full_name, $fb_login_form->email, $fb_login_form->picture, $fb_login_form->facebook_id, User::CLIENT);
                if(!$new_user)
                    return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later']);
                return Helpers::formatResponse(true, 'sign up success', ['auth_key' => $new_user['auth_key']]);
            }

            if(!$user->regGenerateAuthKey()){
                return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later']);
            }
            return Helpers::formatResponse(true, 'log in success', ['auth_key' => $user['auth_key']]);
        }
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

    public function actionRestaurants()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();
        
        if($request->isGet) {
            if(!isset($get_data['id']))
                return Restaurants::getRestaurants();
            else if(!empty($get_data) && isset($get_data['id']))
                return Restaurants::getRestaurantDetails($get_data['id']);
        }

        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function actionCuisines()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if($request->isGet) {
            if(!isset($get_data['id']))
                return Cuisines::getCuisines();
        }

        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function actionMenuItems()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if($request->isGet) {
            if(!empty($get_data) && isset($get_data['id']))
                return MenuItems::getMenuItemForClient($get_data['id']);
        }

        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function actionAddress()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if($request->isGet) {
            if (empty($get_data))
                return Addresses::getAddresses();
            else if (!empty($get_data) && isset($get_data['id']))
                return Addresses::getAddress($get_data['id']);
        } else if($request->isPost && empty($get_data)) {
            if(empty($request->post()))
                return Helpers::HttpException(422,'validation failed', ['error' => 'please provide data']);
            return Addresses::createAddress($request->post());
        } else if($request->isPut && !empty($get_data)) {
            if(empty($request->post()))
                return Helpers::HttpException(422,'validation failed', ['error' => 'please provide data']);
            return Addresses::updateAddress($get_data['id'], $request->post());
        } else if($request->isDelete && !empty($get_data)){
            return Addresses::deleteAddress($get_data['id']);
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
            'restaurants' => ['GET'],
            'menu-items' => ['GET'],
            'cuisines' => ['GET'],
            'address' => ['GET','PUT','POST','DELETE'],
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