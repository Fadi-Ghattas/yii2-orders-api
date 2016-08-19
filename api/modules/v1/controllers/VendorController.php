<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/10/2016
 * Time: 6:05 AM
 */


//https://api.jommakan.asia/

namespace api\modules\v1\controllers;


use Yii;
use common\helpers\Helpers;
use common\models\User;
use common\models\LoginForm;
use common\models\Restaurants;
use common\models\MenuCategories;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
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

        return $behaviors;
    }

    public function actionLogin()
    {
        $post_data = Yii::$app->request->post();

        if (!isset($post_data['email']) || !isset($post_data['password']))
            Helpers::UnprocessableEntityHttpException('validation failed', ['data' => ['email and password are required for login']]);

        $restaurantManager = User::findByEmail($post_data['email']);
        if (empty($restaurantManager))
            throw new NotFoundHttpException('User not found.');
        if (User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            throw new ForbiddenHttpException('This account is not a restaurant account');
        if (!Restaurants::find()->where(['user_id' => $restaurantManager->id])->one()->status)
            throw new ForbiddenHttpException('This account is deactivated');

        $model = new LoginForm();
        $model->username = $post_data['email'];
        $model->password = $post_data['password'];
        $model->email = $post_data['email'];
        if ($model->load($post_data, '') && $model->login()) {
            return Helpers::formatResponse(true,'login success',['auth_key' => $restaurantManager['auth_key']]);
        } else {
            throw new ServerErrorHttpException(strip_tags(Html::errorSummary($model, ['header' => '', 'footer' => ''])));
        }
        Helpers::UnprocessableEntityHttpException('validation failed', ['data' => ['Please provide valid data']]);
    }

    public function actionLogout()
    {
        $post_data = Yii::$app->request->post();
        $headers = Yii::$app->getRequest()->getHeaders();
        $restaurantManager = User::findIdentityByAccessToken(explode(' ', $headers['authorization'])[1]);

        if (empty($restaurantManager))
            throw new NotFoundHttpException('User not found');
        if (User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            throw new ForbiddenHttpException('This account is not a restaurant account');

//        $user = User::findOne($restaurantManager->id);
        $restaurants = Restaurants::find(['=', 'user_id', $restaurantManager->id])->one();
        $restaurants->action = 'logout';

        if ($post_data['password'] != $restaurantManager['password_hash'])
            Helpers::UnprocessableEntityHttpException('validation failed', ['password' => ['The password incorrect']]);

        $transaction = Restaurants::getDb()->beginTransaction();


        try {
//            $user->password_hash = 'RESTAURANT_DEACTIVATED';
//            $user->save(false);
            $restaurants->status = 0;
            $restaurants->save(false);
            $transaction->commit();
            return Helpers::formatResponse(true,'You have been logged out successful' , null);
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new ServerErrorHttpException('Something went wrong please try again..');
            //throw $e;
        }
    }

    public function actionMenu()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if($request->isGet) {
            if(empty($get_data))
                return MenuCategories::getMenuCategories();
            else if(!empty($get_data) && isset($get_data['id']))
                return MenuCategories::getMenuCategoryItemsResponse($get_data['id']);
        } else if($request->isPost && empty($get_data)){
            if(empty($request->post()))
                Helpers::UnprocessableEntityHttpException('validation failed', ['data' => ['please provide data']]);
            return MenuCategories::createCategory($request->post());
        } else if($request->isPut) {
            if(empty(Json::decode($request->getRawBody())))
                Helpers::UnprocessableEntityHttpException('validation failed', ['data' => ['please provide data']]);
            if(!isset($get_data['id']))
                Helpers::UnprocessableEntityHttpException('validation failed', ['data' => ['please provide category id']]);
            return MenuCategories::updateCategory($get_data['id'], Json::decode($request->getRawBody()));
        }

        throw new MethodNotAllowedHttpException("Method Not Allowed");
    }

    public function beforeAction($event)
    {
        $request_action = explode('/', Yii::$app->getRequest()->getUrl());
        $actions = [
            'login' => ['POST'],
            'logout' => ['POST'],
            'menu' => ['GET','PUT','POST']
        ];
        foreach ($actions as $action => $verb) {
            if (in_array($action, $request_action)) {
                if (!in_array(Yii::$app->getRequest()->getMethod(), $actions[$action]))
                    throw new MethodNotAllowedHttpException("Method Not Allowed");
            }
        }
        return parent::beforeAction($event);
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        if(in_array($action->id, ['index','view','update','delete','create','options']))
        {
            $response = array();
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
                default:
                    throw new MethodNotAllowedHttpException("Method Not Allowed");
            }
            return $response;
        }

        return $result;
    }
}