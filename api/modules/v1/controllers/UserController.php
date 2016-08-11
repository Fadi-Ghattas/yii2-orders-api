<?php
/**
 * Created by PhpStorm.
 * User: Aghiad
 * Date: 2015-02-24
 * Time: 2:26 PM
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\User;
use common\models\LogiForm;
use common\models\Rate;
use frontend\models\PasswordResetRequestForm;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;


/**
 * Class UserController
 * @package api\modules\v1\controllers
 * @link api/web/v1/users
 */
class UserController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\User';

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['create', 'login', 'resetpassword'],
                    'authMethods' => [
                        HttpBearerAuth::className(),
                    ],
                ],
            ]
        );
    }

    public function actionResetpassword()
    {

        $post = Yii::$app->request->post();
        $model = User::findOne(["email" => $post["email"]]);
        if ($model) {
            $prmodel = new PasswordResetRequestForm();
            $prmodel->email = $post["email"];
            if ($prmodel->sendEmail()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(200);
                return '{"message":"Check your email for further instructions."}';
            } else {
                throw new ServerErrorHttpException('Sorry, we are unable to reset password for email provided..');
            }
        } else {
            throw new NotFoundHttpException('Email not existed.');

        }

        //return ["auth_key" => $model["auth_key"]];
    }

    public function actionLogin()
    {

        $post = Yii::$app->request->post();

        $model = User::findOne(["email" => $post["email"]]);
        if (empty($model)) {
            throw new \yii\web\NotFoundHttpException('User not found');
        }

        if ($model->validatePassword($post["password"])) {
//            $model->last_login = Yii::$app->formatter->asTimestamp(date_create());
//            $model->save(false);
            return $model;//["auth_key" => $model["auth_key"]];
        } else {
            throw new \yii\web\ForbiddenHttpException();
        }

        //return ["auth_key" => $model["auth_key"]];
    }

    public function checkAccess($action, $model = null, $params = [])
    {

//        if(!(\Yii::$app->user->can('manageUsers')) && $action != 'create' && $action != 'login')
//        {
//            throw new \yii\web\ForbiddenHttpException();
//        }

    }

    public function actions()
    {
        $actions = parent::actions();
//        $actions['create']['scenario'] = 'create_scenario';
//        $actions['update']['scenario'] = 'update_scenario';
//        $actions[] = ['login' =>
//            ['modelClass' => $this->modelClass,
//                'checkAccess' => [$this, 'checkAccess'],
//            ],
//            'resetpassword' =>
//                ['modelClass' => $this->modelClass,
//                    'checkAccess' => [$this, 'checkAccess'],
//                ]
//        ];

        return $actions;
    }
}