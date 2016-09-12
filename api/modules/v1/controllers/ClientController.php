<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 9/11/2016
 * Time: 10:09 PM
 */

namespace api\modules\v1\controllers;

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
                    'except' => ['sign-up'],
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

        $user = new User();
        $model['user'] = $post_data;
        $user->load($model);
//        $user->username = $post_data['username'];
//        $user->email = $post_data['email'];
        $user->last_logged_at = date('Y-m-d H:i:s');
//        $user->setPassword($post_data['password']);
        $user->generateAuthKey();
        $user->role = User::CLIENT;
        $user->validate();
        if(!$user->save())
            return Helpers::HttpException(400,'sing up failed', ['error' => 'something went wrong try again please..']);
        return Helpers::formatResponse(true, 'sing up success', $user);
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