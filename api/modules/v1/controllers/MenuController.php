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
use common\models\MenuCategories;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;
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
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['index']);
        return $actions;
    }

    protected function verbs()
    {
        return [
            'create' => ['POST'],
            'update' => ['PUT'],
            'delete' => ['DELETE'],
            'view' => ['GET'],
            'index' => ['GET'],
        ];
    }

    public function actionIndex()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $restaurantManager = User::findIdentityByAccessToken(explode(' ', $headers['authorization'])[1]);
        $restaurant = Restaurants::find()->where(['user_id' => $restaurantManager->id])->one();

        if(empty($restaurantManager))
            throw new NotFoundHttpException('User not found');
        if(User::getRoleName($restaurantManager->id) != User::RESTAURANT_MANAGER)
            throw new ForbiddenHttpException('This account is not a restaurant account');
        if(!$restaurant->status)
            throw new ForbiddenHttpException('This account is deactivated');

        return ['success' => 'true' , 'message' => 'get success', 'data' => Helpers::formatJsonIdName($restaurant->menuCategories)];
    }

    public function actionView()
    {
        return 1;
    }



    public function afterAction($action, $result)
    {
        $response = array();
        $result = parent::afterAction($action, $result);

        switch ($action->id) {

        }

        return $result;
    }
}