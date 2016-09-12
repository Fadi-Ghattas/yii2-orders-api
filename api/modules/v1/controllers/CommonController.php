<?php
/**
 * Created by PhpStorm.
 * User: Fadi
 * Date: 8/24/2016
 * Time: 11:03 PM
 */

namespace api\modules\v1\controllers;

use Yii;
use common\models\Countries;
use common\models\States;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\MethodNotAllowedHttpException;

class CommonController extends ActiveController
{
    public $modelClass = '';

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

    public function actionCountries()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if($request->isGet) {
            if(empty($get_data))
                return Countries::getCountries();
        }

        throw new MethodNotAllowedHttpException("Method Not Allowed");
    }

    public function actionStates()
    {
        $request = Yii::$app->request;
        $get_data = $request->get();

        if($request->isGet) {
            if(empty($get_data))
                return States::getStates();
        }

        throw new MethodNotAllowedHttpException("Method Not Allowed");    
    }

    public function beforeAction($event)
    {
        $request_action = explode('/', Yii::$app->getRequest()->getUrl());
        $actions = [
            'countries' => ['GET'],
            'states' => ['GET'],
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