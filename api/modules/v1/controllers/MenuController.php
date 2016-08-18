<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/17/2016
 * Time: 10:56 PM
 */
namespace api\modules\v1\controllers;

use Yii;
use common\helpers\Helpers;
use common\models\User;
use common\models\Restaurants;
use yii\helpers\Html;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\NotFoundHttpException;
class MenuController extends ActiveController
{
    public $modelClass = 'common\models\MenuCategories';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBearerAuth::className(),
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        //unset($actions['delete'], $actions['create']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'getRestaurantMenu'];
        $actions['view']['prepareDataProvider'] = [$this, 'getRestaurantMenuItems'];

        return $actions;
    }

    public function getRestaurantMenu()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $restaurantManager = User::findIdentityByAccessToken(explode(' ',$headers['authorization'])[1]);
        $restaurant = Restaurants::find()->where(['user_id' => $restaurantManager->id])->one();

        return $restaurant->menuCategories;
    }

    public function getRestaurantMenuItems()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $restaurantManager = User::findIdentityByAccessToken(explode(' ',$headers['authorization'])[1]);
//        $restaurant = Restaurants::find()->where(['user_id' => $restaurantManager->id])->one();
    }
    
//    public function actionMenu()
//    {
//        $headers = Yii::$app->getRequest()->getHeaders();
//        $restaurantManager = User::findIdentityByAccessToken(explode(' ',$headers['authorization'])[1]);
//        $restaurant
//    }

    public function afterAction($action, $result)
    {
        $response = array();
        $result = parent::afterAction($action, $result);

        switch ($action->id) {

        }

        return $result;
    }
}