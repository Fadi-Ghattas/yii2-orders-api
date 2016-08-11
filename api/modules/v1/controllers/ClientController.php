<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 7/28/2016
 * Time: 3:41 PM
 */

namespace api\modules\v1\controllers;


use Yii;
use common\models\User;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\base\UserException;
use yii\web\ServerErrorHttpException;
use yii\web\NotFoundHttpException;

class ClientController extends ActiveController
{
    public $modelClass = 'common\models\Restaurants';


    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['login'],
                    'authMethods' => [
                        HttpBearerAuth::className(),
                    ],
                ],
            ]
        );
    }

    public function actionLogin()
    {
        $post_data = Yii::$app->request->post();
        $model = new LoginForm();
        $model->username = $post_data['email'];
        if ($model->load($post_data, '') && $model->login()) {
            $Client = User::getClient($post_data['email']);
            if (empty($Client))
                throw new NotFoundHttpException('User not found.');
            return $Client;
        } else {
            throw new UserException('Please provide valid data.');
//            return $model->getErrors();
        }
        throw new UserException('Please provide valid data.');
    }

    public function actionResetPassword()
    {
        $post_data = Yii::$app->request->post();
        $model = Client::getClient(["email" => $post_data['email']]);

        if ($model) {
            $prmodel = new PasswordResetRequestForm();
            $prmodel->email = $post_data['email'];
            if ($prmodel->sendEmail()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(200);
                $data['status'] = 'true';
                $data['message'] = 'Check your email for further instructions.';
                return $data;
            } else {
                throw new ServerErrorHttpException('Something went wrong please try again..');
            }
        } else {
            throw new NotFoundHttpException('Email not existed.');
        }

        throw new UserException('Please provide valid data.');
    }

    public function actionChangePassword()
    {
        $post_data = Yii::$app->request->post();
        $model = Client::getClient(["email" => $post_data['email']]);

        if (!empty($model)) {
            if ($model->validatePassword($post_data["old_password"])) {
                $model->setPassword($post_data["new_password"]);
                if ($model->save(false)) {
                    return ['status' => 'true'];
                } else {
//                return $model->getErrors();
                    throw new ServerErrorHttpException('Something went wrong please try again..');
                }
            } else {
                throw new UserException('The old password incorrect.');
            }
        } else {
            throw new NotFoundHttpException('Email not existed.');
        }
        throw new UserException('Please provide valid data.');
    }

    public function actionGetClientProjects()
    {
        $post_data = Yii::$app->request->post();
        $user = Client::getClientByID($post_data['client_id']);
        if (!is_null($user)) {
            return Client::getClientProjects($post_data['client_id']);
        } else {
            throw new NotFoundHttpException('Client not found.');
        }

        throw new ServerErrorHttpException('Please provide valid data.');
    }
}