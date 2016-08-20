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

        if(empty($post_data))
            Helpers::UnprocessableEntityHttpException('validation failed', ['data' => ['Please provide data']]);

        if(!isset($post_data['email']) || !isset($post_data['password']))
            Helpers::UnprocessableEntityHttpException('validation failed', ['data' => ['The email and password are required']]);

        $restaurantManager = User::findIdentityByAccessToken(explode(' ', $headers['authorization'])[1]);
        if (empty($restaurantManager))
            throw new NotFoundHttpException('User not found');

        if($post_data['email'] !=  $restaurantManager->email || $post_data['password'] != $restaurantManager->password_hash)
            Helpers::UnprocessableEntityHttpException('validation failed', ['data' => ['The email or password incorrect']]);

        if (User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            throw new ForbiddenHttpException('This account is not a restaurant account');

//        $user = User::findOne($restaurantManager->id);
        $restaurants = Restaurants::find(['=', 'user_id', $restaurantManager->id])->one();
        $restaurants->action = 'logout';

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
//            throw new ServerErrorHttpException('Something went wrong please try again..');
            throw $e;
        }
    }

    public function actionProfile()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if($request->isGet && empty($get_data)) {
            return Helpers::formatResponse(true, 'get success', Restaurants::checkRestaurantAccess()) ;
        }else if($request->isPut && empty($get_data)) {
            if(empty($request->post()))
                Helpers::UnprocessableEntityHttpException('validation failed', ['data' => ['please provide data']]);
            return Restaurants::updateRestaurant($request->post());
        }
        throw new MethodNotAllowedHttpException("Method Not Allowed");
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
        } else if($request->isPut && !empty($get_data)) {
            if(empty($request->post()))
                Helpers::UnprocessableEntityHttpException('validation failed', ['data' => ['please provide data']]);
            return MenuCategories::updateCategory($get_data['id'], $request->post());
        } else if($request->isDelete && !empty($get_data)){
            return MenuCategories::deleteCategory($get_data['id']);
        }

        throw new MethodNotAllowedHttpException("Method Not Allowed");
    }

    public function beforeAction($event)
    {
        $request_action = explode('/', Yii::$app->getRequest()->getUrl());
        $actions = [
            'login' => ['POST'],
            'logout' => ['POST'],
            'menu' => ['GET','PUT','POST','DELETE'],
            'profile' => ['GET','PUT']
        ];

        foreach ($actions as $action => $verb) {
            if (in_array($action, $request_action)) {
                if (!in_array(Yii::$app->getRequest()->getMethod(), $actions[$action]))
                    throw new MethodNotAllowedHttpException("Method Not Allowed");
            }
        }
        return parent::beforeAction($event);
    }

//    public function afterAction($action, $result)
//    {
//        $result = parent::afterAction($action, $result);
//
//        if(in_array($action->id, ['index','view','update','delete','create','options']))
//        {
//            $request = Yii::$app->request;
//            $get_data = $request->get();
//            $response = array();
//            switch ($action->id) {
//                case 'view':
//                    if($request->isGet && empty($get_data)){
//                        $response['success'] = true;
//                        $response['message'] = 'get success';
//                        $response['data'] = $result;
//                    } else {
//                        throw new MethodNotAllowedHttpException("Method Not Allowed");
//                    }
//                    break;
//                case 'update':
//                    $response['success'] = true;
//                    $response['message'] = 'update success';
//                    $response['data']['id'] = $result['id'];
//                    break;
//                default:
//                    throw new MethodNotAllowedHttpException("Method Not Allowed");
//            }
//            return $response;
//        }
//
//        return $result;
//    }
}