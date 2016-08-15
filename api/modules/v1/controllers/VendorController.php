<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/10/2016
 * Time: 6:05 AM
 */

namespace api\modules\v1\controllers;

use common\models\Restaurants;
use Yii;
use common\models\User;
use common\models\LoginForm;
use yii\helpers\Html;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\base\UserException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\NotFoundHttpException;

class VendorController extends ActiveController
{
    public $modelClass = 'common\models\Restaurants';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'except' => ['login'],
            'authMethods' => [
                HttpBearerAuth::className(),
            ],
        ];

        $behaviors['verbs'] = [
            'class' => \yii\filters\VerbFilter::className(),
            'actions' => [
                'index'  => [],
                'login' => ['post'],
                'view'   => ['get'],
                'create' => [],
                'update' => ['put'],
                'delete' => []
            ],
        ];

        return $behaviors;
    }

    public function actionLogin()
    {
        $post_data = Yii::$app->request->post();

        if(!isset($post_data['email']) || !isset($post_data['password']))
            throw new UserException('email and password are required for login.');

        $model = new LoginForm();
        $model->username = $post_data['email'];
        $model->password = $post_data['password'];
        $model->email = $post_data['email'];
        if ($model->load($post_data, '') && $model->login()) {
            $restaurantManager = User::findByEmail($post_data['email']);
            if(empty($restaurantManager)){
                throw new NotFoundHttpException('User not found.');
            }
            if(User::getRoleName(Yii::$app->user->id) != User::RESTAURANT_MANAGER)
                throw new ForbiddenHttpException('This account is not a restaurant account.');

            return ['auth_key' => $restaurantManager['auth_key']];

        } else {
            throw new UserException(strip_tags(Html::errorSummary($model, ['header' => '', 'footer' => ''])));
        }
        throw new UserException('Please provide valid data.');
    }

    public function actionLogout()
    {
        $post_data = Yii::$app->request->post();
        $headers = Yii::$app->getRequest()->getHeaders();
        $restaurantManager = User::findIdentityByAccessToken(explode(' ',$headers['authorization'])[1]);

        if(empty($restaurantManager))
            throw new NotFoundHttpException('User not found.');
        if(User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            throw new ForbiddenHttpException('This account is not a restaurant account.');
        if($post_data['password'] != $restaurantManager['password_hash'])
            throw new UserException('The password incorrect.');

        $user = User::findOne($restaurantManager->id);
        $restaurants = Restaurants::find(['=','user_id',$restaurantManager->id])->one();

        $transaction = User::getDb()->beginTransaction();

        try {
            $user->password_hash = 'RESTAURANT_DEACTIVATED';
            $user->save(false);
            $restaurants->status = 0;
            $restaurants->save(false);
            $transaction->commit();
            return ['status' => 'true'];
        } catch(\Exception $e) {
            $transaction->rollback();
            throw new ServerErrorHttpException('Something went wrong please try again..');
            //throw $e;
        }
    }

    public function beforeAction($event)
    {
        $request_action = explode('/',Yii::$app->getRequest()->getUrl());
        $actions = ['login' => ['POST']];
        foreach ($actions as $action => $verb)
        {
            if(in_array($action , $request_action)){
                if( !in_array(Yii::$app->getRequest()->getMethod() , $actions[$action]))
                    throw new \yii\web\MethodNotAllowedHttpException('method not allowed');
            }
        }
        return parent::beforeAction($event);
    }

    public function afterAction($action, $result)
    {
        $response = array();
        $result = parent::afterAction($action, $result);

        switch ($action->id) {
            case 'view':
                $response['success'] = true;
                $response['message'] = 'get success';
                $response['data'] = $result;
                break;
            case 'update':
                $response['success'] = true;
                $response['message'] = 'update success';
                $response['data']['id'] = $result['id'];
                break;
            case 'login':
                $response['success'] = true;
                $response['message'] = 'login success';
                $response['data'] = $result;
                break;
            default:
                $response['success'] = false;
                $response['message'] = "You don't have permission to do this action.";
                $response['data'] = null;
        }

        return $response;
    }
}