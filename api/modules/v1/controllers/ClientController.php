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
use common\models\Clients;
use common\models\NewRestaurant;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;

class ClientController extends ActiveController
{
    public $modelClass = 'common\models\User';

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

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['sign-up',
                        'log-in',
                        'facebook-login',
                        'restaurants',
                        'menu-items',
                        'cuisines',
                        'reset-password-sms-code',
                        'reset-password'],
                    'authMethods' => [
                        HttpBearerAuth::className(),
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $client = Clients::getClientByAuthorization();
        return Helpers::formatResponse(true, 'get success', $client->user->getUserClientFields());
    }

    public function actionOptions()
    {
        $post_data = Yii::$app->request->post();
        $clientData = Clients::updateClient($post_data);
        return Helpers::formatResponse(true, 'update success', $clientData);
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
            if (!$new_user)
                return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later']);
            return Helpers::formatResponse(true, 'sign up success', $new_user->getUserClientFields());
        }
        return Helpers::HttpException(501, 'not implemented', ['error' => 'Something went wrong, try again later or contact the admin.']);
    }

    public function actionLogIn()
    {
        $post_data = Yii::$app->request->post();
        if (!isset($post_data['source']))
            return Helpers::HttpException(422, 'validation failed', ['error' => "source is required"]);
        if (empty($post_data['source']))
            return Helpers::HttpException(422, 'validation failed', ['error' => "source can't be blank"]);

        if ($post_data['source'] == User::SOURCE_BASIC) {
            $login_form = new LoginForm();
            $login_form->setAttributes($post_data);
            if (!$login_form->validate()) {
                return Helpers::HttpException(422, 'validation failed', ['error' => $login_form->errors]);
            }
            $user = User::Login($login_form->email, $login_form->password);
            if (!$user)
                return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);
            return Helpers::formatResponse(true, 'log in success', $user->getUserClientFields());
        } else if ($post_data['source'] == User::SOURCE_FACEBOOK) {
            $fb_login_form = new FacebookLoginForm();
            $fb_login_form->setAttributes($post_data);

            if (!$fb_login_form->validate()) {
                return Helpers::HttpException(422, 'validation failed', ['error' => $fb_login_form->errors]);
            }

            $user = User::findOne(['email' => $fb_login_form->email]);

            $fb_verify = User::VerifyFB($fb_login_form->email, $fb_login_form->facebook_id, $fb_login_form->access_token);
            if (!$fb_verify)
                return Helpers::HttpException(422, 'facebook validation failed', ['error' => 'facebook id or email verification failed']);

            if (!$user) {
                $new_user = User::NewFacebookSignUp($fb_login_form->full_name, $fb_login_form->email, $fb_login_form->picture, $fb_login_form->facebook_id, User::CLIENT);
                if (!$new_user)
                    return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later']);
                return Helpers::formatResponse(true, 'sign up success', $new_user->getUserClientFields());
            }

            if (!$user->regGenerateAuthKey()) {
                return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later']);
            }

            $client = Clients::findOne(['user_id' => $user]);
            $client->setScenario(User::SCENARIO_SIGN_UP_FACEBOOK);
            $client->image = $post_data['picture'];
            $user->username = $post_data['full_name'];
            $user->email = $post_data['email'];
            if (!$user->save())
                return Helpers::HttpException(422, 'validation failed', ['error' => $user->errors]);
            if (!$client->save())
                return Helpers::HttpException(422, 'validation failed', ['error' => $client->errors]);

            return Helpers::formatResponse(true, 'log in success', $user->getUserClientFields());
        }
    }

    public function actionLogOut()
    {
        $headers = Yii::$app->getRequest()->getHeaders();

        $clientUser = User::findIdentityByAccessToken(explode(' ', $headers['authorization'])[1]);
        if (empty($clientUser))
            return Helpers::HttpException(404, 'not found', ['error' => 'user not found']);

        $clientUser->auth_key = '';
        if (!$clientUser->save(false))
            return Helpers::HttpException(500, 'server error', ['error' => 'Something went wrong, try again later.']);

        return Helpers::formatResponse(true, 'log out success', null);
    }

    public function actionRestaurants()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if ($request->isGet) {
            if (!isset($get_data['id']))
                return Restaurants::getRestaurants();
            else if (!empty($get_data) && isset($get_data['id']))
                return Restaurants::getRestaurantDetails($get_data['id']);
        }

        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function actionCuisines()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if ($request->isGet) {
            if (!isset($get_data['id']))
                return Cuisines::getCuisines();
        }

        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function actionMenuItems()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if ($request->isGet) {
            if (!empty($get_data) && isset($get_data['id']))
                return MenuItems::getMenuItemForClient($get_data['id']);
        }

        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function actionAddress()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if ($request->isGet) {
            if (empty($get_data))
                return Addresses::getAddresses();
            else if (!empty($get_data) && isset($get_data['id']))
                return Addresses::getAddress($get_data['id']);
        } else if ($request->isPost && empty($get_data)) {
            if (empty($request->post()))
                return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
            return Addresses::createAddress($request->post());
        } else if ($request->isPut && !empty($get_data)) {
            if (empty($request->post()))
                return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
            return Addresses::updateAddress($get_data['id'], $request->post());
        } else if ($request->isDelete && !empty($get_data)) {
            return Addresses::deleteAddress($get_data['id']);
        }

        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function actionSmsCode()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if ($request->isGet) {
            return Clients::sendVerificationSmsCode();
        }
        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function actionResetPasswordSmsCode()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if ($request->isPost && empty($get_data)) {
            if (empty($request->post()))
                return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
            return Clients::resetPasswordSmsCode($request->post());
        }
        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function actionResetPassword()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if ($request->isPost && empty($get_data)) {
            if (empty($request->post()))
                return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
            return Clients::resetPassword($request->post());
        }
        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function actionChangePassword()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if ($request->isPost && empty($get_data)) {
            if (empty($request->post()))
                return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
            return Clients::changePassword($request->post());
        }
        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function actionNewRestaurant()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if ($request->isPost && empty($get_data)) {
            if (empty($request->post()))
                return Helpers::HttpException(422, 'validation failed', ['error' => 'please provide data']);
            return NewRestaurant::createNewRestaurant($request->post());
        }
        return Helpers::HttpException(405, "Method Not Allowed", null);
    }

    public function beforeAction($event)
    {
        $request_action = explode('/', Yii::$app->getRequest()->getUrl());
        $actions = [
            'index' => ['GET'],
            'options' => ['PUT'],
            'sign-up' => ['POST'],
            'log-in' => ['POST'],
            'log-out' => ['POST'],
            'restaurants' => ['GET'],
            'menu-items' => ['GET'],
            'cuisines' => ['GET'],
            'address' => ['GET', 'PUT', 'POST', 'DELETE'],
            'sms-code' => ['GET'],
            'reset-password-sms-code' => ['POST'],
            'reset-password' => ['POST'],
            'change-password' => ['POST'],
            'new-restaurant' => ['POST'],
        ];

        foreach ($actions as $action => $verb) {
            if (in_array($action, $request_action) || $action == $event->id) {
                if (!in_array(Yii::$app->getRequest()->getMethod(), $actions[$action]))
                    return Helpers::HttpException(405, "Method Not Allowed", null);
            }
        }
        return parent::beforeAction($event);
    }

}