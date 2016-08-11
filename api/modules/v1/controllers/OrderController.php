<?php
/**
 * Created by PhpStorm.
 * User: yousef
 * Date: 23/06/16
 * Time: 12:31 PM
 */

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use common\models\Order;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;


/**
 * Class OrderController
 * @order api\modules\v1\controllers
 * @link api/web/v1/order
 */
class OrderController extends ActiveController
{
    public $modelClass = 'common\models\Order';
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
                'authenticator' => [
                    'class' => CompositeAuth::className(),
                    'except' => ['index'],
                    'authMethods' => [
                        HttpBearerAuth::className(),
                    ],
                ],
            ]
        );
    }

    public function actionMy()
    {
        return Order::getMyOrders();
    }
    public function actionCurrent()
    {
        return Order::getCurrentOrder();
    }
}