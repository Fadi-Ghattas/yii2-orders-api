<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 9/11/2016
 * Time: 10:09 PM
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\SignUpForm;
use common\helpers\Helpers;
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
                    'except' => ['sing-up'],
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

    public function actionSingUp()
    {
        $post_data = Yii::$app->request->post();
        $sing_up_form = new SignUpForm();
        $sing_up_form->setAttributes($post_data);
        if (!$sing_up_form->validate()) {
            return Helpers::HttpException(422, 'validation failed', ['error' => $sing_up_form->errors]);
        }
        $user = User::findOne(['email' => $sing_up_form->email]);
        if (!$user) {
            $new_user = User::NewBasicSignUp($sing_up_form->username, $sing_up_form->email, $sing_up_form->password);
            if(!$new_user)
                return Helpers::HttpException(500,'validation failed', ['error' => 'Something went wrong, try again later.']);
            return Helpers::formatResponse(true, 'post success', $new_user) ;
        } else {//TODO if we add the email verification check this again.
            return Helpers::HttpException(422, 'validation failed', ['error' => 'this email already taken please try another.']);
        }
        return Helpers::HttpException(501,'validation failed', ['error' => 'Something went wrong, try again later or contact the admin.']);
    }

    public function beforeAction($event)
    {
        $request_action = explode('/', Yii::$app->getRequest()->getUrl());
        $actions = [
            'sing-up' => ['POST'],
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